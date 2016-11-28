<?php

/**
 * DAO for {@link Item}.
 * <p>
 * It is also a service, ideally, this class should be divided into DAO and Service.
 */
final class ItemDao {

    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link Item}s by search criteria.
     * @return array array of {@link Item}s
     */
    public function find(ItemSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $item = new Item();
            ItemMapper::map($item, $row);
            $result[$item->getId()] = $item;
        }
        return $result;
    }

    /**
     * Find {@link Item} by identifier.
     * @return Item or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT * FROM items WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $item = new Item();
        ItemMapper::map($item, $row);
        return $item;
    }

    /**
     * @return array of item id, type, sub_type
     */
    public function getAllItemIdAndType() {
        $statement = $this->getDb()->prepare('SELECT id, item_type, sub_type
                        FROM items ORDER BY item_type, sub_type');
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /**
     * Save {@link Item}.
     * @param Item $item {@link Item} to be saved
     * @return Item saved {@link Item} instance
     */
    public function save(Item $item) {
        if ($item->getId() === null) {
            return $this->insert($item);
        }
        return $this->update($item);
    }

    /**
     * Delete {@link Item} by identifier.
     * @param int $id {@link Item} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM items 
            WHERE
                id = :id';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            ':id' => $id,
        ));
        return $statement->rowCount() == 1;
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

    private function getFindSql(ItemSearchCriteria $search = null) {
        $sql = 'SELECT * FROM items ';
        $orderBy = ' item_type, sub_type';
        $sql .= ' ORDER BY ' . $orderBy;
        return $sql;
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function insert(Item $item) {
        $item->setId( null );
        $sql = '
            INSERT INTO items (id, item_type, sub_type, size,  unit)
                VALUES (:id, :item_type, :sub_type, :size, :unit)';
        return $this->execute($sql, $item);
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function update(Item $item) {
        $sql = '
            UPDATE items SET
                item_type = :item_type,
                sub_type = :sub_type,
                size = :size,
                unit = :unit
            WHERE
                id = :id';
        return $this->execute($sql, $item);
    }

    /**
     * @return Item
     * @throws Exception
     */
    private function execute($sql, Item $item) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($item));
        if (!$item->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        if (!$statement->rowCount()) {
            throw new NotFoundException('Item with ID "' . $item->getId() . '" does not exist.');
        }
        return $item;
    }

    private function getParams(Item $item) {
        $params = array(
            ':id' => $item->getId(),
            ':item_type' => $item->getType(),
            ':sub_type' => $item->getSubType(),
            ':size' => $item->getSize(),
            ':unit' => $item->getUnit()
        );
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
