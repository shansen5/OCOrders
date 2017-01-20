<?php

/**
 * DAO for {@link Account}.
 * <p>
 * It is also a service, ideally, this class should be divided into DAO and Service.
 */
final class AccountDao {

    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link Account}s by search criteria.
     * @return array array of {@link Account}s
     */
    public function find(AccountSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $account = new Account();
            AccountMapper::map($account, $row);
            $result[$account->getId()] = $account;
        }
        return $result;
    }

    /**
     * Find {@link Account} by identifier.
     * @return Account or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT * FROM accounts WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $account = new Account();
        AccountMapper::map($account, $row);
        return $account;
    }

    /**
     * @return array of account id, name
     */
    public function getAllAccountIdAndName() {
        $statement = $this->getDb()->prepare('SELECT id, name
                        FROM accounts ORDER BY name');
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /**
     * Save {@link Account}.
     * @param Account $account {@link Account} to be saved
     * @return Account saved {@link Account} instance
     */
    public function save(Account $account) {
        if ($account->getId() === null) {
            return $this->insert($account);
        }
        return $this->update($account);
    }

    /**
     * Delete {@link Account} by identifier.
     * @param int $id {@link Account} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM accounts 
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

    private function getFindSql(AccountSearchCriteria $search = null) {
        $sql = 'SELECT * FROM accounts ';
        $orderBy = ' name';
        $sql .= ' ORDER BY ' . $orderBy;
        return $sql;
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function insert(Account $account) {
        $account->setId( null );
        $sql = '
            INSERT INTO accounts (id, name, default_customer_id)
                VALUES (:id, :name, :default_customer_id )';
        return $this->execute($sql, $account);
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function update(Account $account) {
        $sql = '
            UPDATE accounts SET
                name = :name,
                default_customer_id = :default_customer_id
            WHERE
                id = :id';
        return $this->execute($sql, $account);
    }

    /**
     * @return Account
     * @throws Exception
     */
    private function execute($sql, Account $account) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($account));
        if (!$account->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        if (!$statement->rowCount()) {
            throw new NotFoundException('Account with ID "' . $account->getId() . '" does not exist.');
        }
        return $account;
    }

    private function getParams(Account $account) {
        $params = array(
            ':id' => $account->getId(),
            ':name' => $account->getName(),
            ':default_customer_id' => $account->getDefaultCustomerId()
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

}
