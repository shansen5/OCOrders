<?php

/**
 * DAO for {@link Location}.
 * <p>
 * It is also a service, ideally, this class should be divided into DAO and Service.
 */
final class LocationDao {

    const ADDRESS_INSERT = 1;
    const LOCATION_INSERT = 2;
    
    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link Location}s by search criteria.
     * @return array array of {@link Location}s
     */
    public function find(LocationSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $location = new Location();
            LocationMapper::map($location, $row);
            $result[$location->getId()] = $location;
        }
        return $result;
    }

    /**
     * Find {@link Location} by identifier.
     * @return Location or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT l.id, l.address_id, l.name, l.zone, a.street1, a.street2, a.city, 
                                        a.state, a.postal_code, a.country
                            FROM locations l, addresses a
                            WHERE l.address_id = a.id AND l.id = '
                 . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $location = new Location();
        LocationMapper::map($location, $row);
        return $location;
    }

    /**
     * @return array of location id, name
     */
    public function getAllLocationIdAndName() {
        $statement = $this->getDb()->prepare('SELECT id, name
                        FROM locations ORDER BY name');
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /**
     * Save {@link Location}.
     * @param Location $location {@link Location} to be saved
     * @return Location saved {@link Location} instance
     */
    public function save(Location $location) {
        if ($location->getId() === null) {
            return $this->insert($location);
        }
        return $this->update($location);
    }

    /**
     * Delete {@link Location} by identifier.
     * @param int $id {@link Location} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM addresses 
            WHERE id IN ( SELECT address_id FROM locations WHERE id = :id)';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            ':id' => $id,
        ));
        $sql2 = '
            DELETE FROM locations 
            WHERE
                id = :id';
        $statement2 = $this->getDb()->prepare($sql2);
        $this->executeStatement($statement2, array(
            ':id' => $id,
        ));
        return $statement2->rowCount() == 1;
    }

    /**
     * @return PDO
     */
    private function getDb() {
        if ($this->db !== null) {
            return $this->db;
        }
        $config = Config::getConfig("db");
        try {
            $this->db = new PDO($config['dsn'], $config['username'], $config['password'], 
                    array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return $this->db;
    }

    private function getFindSql(LocationSearchCriteria $search = null) {
        $sql = 'SELECT l.id, l.address_id, l.name, l.zone, a.street1, a.street2, a.city, 
                                        a.state, a.postal_code, a.country
                            FROM locations l, addresses a
                            WHERE l.address_id = a.id ';
        $orderBy = ' name';
        $sql .= ' ORDER BY ' . $orderBy;
        return $sql;
    }

    /**
     * @return Location
     * @throws Exception
     */
    private function insert(Location $location) {
        $location->setId( null );
        $location->setAddressId( null );
        $sql = 'INSERT INTO addresses (id, street1, street2, city, 
                                        state, postal_code, country)
                VALUES (:address_id, :street1, :street2, :city, :state,
                    :postal_code, :country)';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($location, self::ADDRESS_INSERT));

        $sql = 'INSERT INTO locations (id, name, zone, address_id)
                VALUES (:id, :name, :zone, ' . $this->getDb()->lastInsertId() . ')';
        $result = $this->execute($sql, $location, self::LOCATION_INSERT);
       
        return $result;
    }

    /**
     * @return Location
     * @throws Exception
     */
    private function update(Location $location) {
        $sql = '
            UPDATE addresses SET
                street1 = :street1,
                street2 = :street2,
                city = :city,
                state = :state,
                postal_code = :postal_code,
                country = :country
            WHERE
                id = :address_id';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($location, self::ADDRESS_INSERT));
        $sql2 = '
            UPDATE locations SET
                name = :name,
                zone = :zone
            WHERE
                id = :id';
        return $this->execute($sql2, $location, self::LOCATION_INSERT);
    }

    /**
     * @return Location
     * @throws Exception
     */
    private function execute($sql, Location $location, $query_type) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($location, $query_type));
        if (!$location->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        if (!$statement->rowCount()) {
            throw new NotFoundException('Location with ID "' . $location->getId() . '" does not exist.');
        }
        return $location;
    }

    private function getParams(Location $location, $query_type ) {
        $params = array();
        switch( $query_type ) {
            case self::ADDRESS_INSERT:
                $params = array(
                    ':address_id' => $location->getAddressId(),
                    ':street1' => $location->getStreet1(),
                    ':street2' => $location->getStreet2(),
                    ':city' => $location->getCity(),
                    ':state' => $location->getState(),
                    ':postal_code' => $location->getPostalCode(),
                    ':country' => $location->getCountry()
                    );
                break;
            case self::LOCATION_INSERT:
                $params = array(
                    ':id' => $location->getId(),
                    ':name' => $location->getName(),
                    ':zone' => $location->getZone()
                    );
                break;
            default:
                break;
        }
        return $params;
    }

    private function executeStatement(PDOStatement $statement, array $params) {
        if (!$statement->execute($params)) {
            $errorInfo = $this->getDb()->errorInfo();
            self::throwDbError( $errorInfo );
        }
    }

    /**
     * @return PDOStatement
     */
    private function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        if ($statement === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
        return $statement;
    }

    private static function throwDbError(array $errorInfo) {
        // TODO log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }

    private static function formatDateTime(DateTime $date) {
        return $date->format(DateTime::ISO8601);
    }

}
