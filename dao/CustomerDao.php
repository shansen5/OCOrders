<?php

/**
 * DAO for {@link Customer}.
 * <p>
 * It is also a service, ideally, this class should be divided into DAO and Service.
 */
final class CustomerDao {

    const ADDRESS_INSERT = 1;
    const CUSTOMER_INSERT = 2;
    
    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link Customer}s by search criteria.
     * @return array array of {@link Customer}s
     */
    public function find(CustomerSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $customer = new Customer();
            CustomerMapper::map($customer, $row);
            $result[$customer->getId()] = $customer;
        }
        return $result;
    }

    /**
     * Find {@link Customer} by identifier.
     * @return Customer or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT l.id, l.address_id, l.first_name, l.last_name, l.phone, l.email,
                        a.street1, a.street2, a.city, a.state, a.postal_code, a.country
                        FROM customers l, addresses a
                        WHERE l.address_id = a.id AND l.id = '
                 . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $customer = new Customer();
        CustomerMapper::map($customer, $row);
        return $customer;
    }

    /**
     * @return array of customer id and name
     */
    public function getAllCustomerIdAndName() {
        $statement = $this->getDb()->prepare('SELECT id, first_name, last_name
                        FROM customers ORDER BY last_name, first_name');
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /**
     * Save {@link Customer}.
     * @param Customer $customer {@link Customer} to be saved
     * @return Customer saved {@link Customer} instance
     */
    public function save(Customer $customer) {
        if ($customer->getId() === null) {
            return $this->insert($customer);
        }
        return $this->update($customer);
    }

    /**
     * Delete {@link Customer} by identifier.
     * @param int $id {@link Customer} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM addresses 
            WHERE id IN ( SELECT address_id FROM customers WHERE id = :id)';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            ':id' => $id,
        ));
        $sql2 = '
            DELETE FROM customers 
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

    private function getFindSql(CustomerSearchCriteria $search = null) {
        $sql = 'SELECT l.id, l.address_id, l.first_name, l.last_name, l.phone, l.email,
                        a.street1, a.street2, a.city, a.state, a.postal_code, a.country
                        FROM customers l, addresses a
                        WHERE l.address_id = a.id ';
        $orderBy = ' last_name';
        $sql .= ' ORDER BY ' . $orderBy;
        return $sql;
    }

    /**
     * @return Customer
     * @throws Exception
     */
    private function insert(Customer $customer) {
        $customer->setId( null );
        $customer->setAddressId( null );
        $sql = 'INSERT INTO addresses (id, street1, street2, city, 
                                        state, postal_code, country)
                VALUES (:address_id, :street1, :street2, :city, :state,
                    :postal_code, :country)';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($customer, self::ADDRESS_INSERT));

        $sql = 'INSERT INTO customers (id, first_name, last_name, phone, email, address_id)
                VALUES (:id, :first_name, :last_name, :phone, :email, ' . $this->getDb()->lastInsertId() . ')';
        $result = $this->execute($sql, $customer, self::CUSTOMER_INSERT);
       
        return $result;
    }

    /**
     * @return Customer
     * @throws Exception
     */
    private function update(Customer $customer) {
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
        $this->executeStatement($statement, $this->getParams($customer, self::ADDRESS_INSERT));
        $sql2 = '
            UPDATE customers SET
                first_name = :first_name,
                last_name = :last_name,
                phone = :phone,
                email = :email
            WHERE
                id = :id';
        return $this->execute($sql2, $customer, self::CUSTOMER_INSERT);
    }

    /**
     * @return Customer
     * @throws Exception
     */
    private function execute($sql, Customer $customer, $query_type) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($customer, $query_type));
        if (!$customer->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        
        if (!$statement->rowCount()) {
            throw new NotFoundException('Customer with ID "' . $customer->getId() . '" does not exist.');
        }
        
        return $customer;
    }

    private function getParams(Customer $customer, $query_type ) {
        $params = array();
        switch( $query_type ) {
            case self::ADDRESS_INSERT:
                $params = array(
                    ':address_id' => $customer->getAddressId(),
                    ':street1' => $customer->getStreet1(),
                    ':street2' => $customer->getStreet2(),
                    ':city' => $customer->getCity(),
                    ':state' => $customer->getState(),
                    ':postal_code' => $customer->getPostalCode(),
                    ':country' => $customer->getCountry()
                    );
                break;
            case self::CUSTOMER_INSERT:
                $params = array(
                    ':id' => $customer->getId(),
                    ':first_name' => $customer->getFirstName(),
                    ':last_name' => $customer->getLastName(),
                    ':phone' => $customer->getPhone(),
                    ':email' => $customer->getEmail()
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
