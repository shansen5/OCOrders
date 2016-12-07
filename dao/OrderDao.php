<?php

/**
 * DAO for {@link Order}.
 * <p>
 * It is also a service, ideally, this class should be divided into DAO and Service.
 */
final class OrderDao {

    const ORDER_INSERT = 1;
    const ORDER_UPDATE = 2;
    
    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link Order}s by search criteria.
     * @return array array of {@link Order}s
     */
    public function find(OrderSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $order = new Order();
            OrderMapper::map($order, $row);
            $result[$order->getId()] = $order;
        }
        return $result;
    }

    /**
     * Find {@link Order} by identifier.
     * @return Order or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT id, customer_id, item_id,
                        start_date, end_date, n_weekly, daily_skip,
                        frequency, day_of_week, pickup_location_id, pickup_time,
                        quantity, order_date, user_id
                        FROM orders 
                        WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $order = new Order();
        OrderMapper::map($order, $row);
        return $order;
    }

    /**
     * Save {@link Order}.
     * @param Order $order {@link Order} to be saved
     * @return Order saved {@link Order} instance
     */
    public function save(Order $order) {
        if ( $order->getId() === null || $order->getId() == 0 ) {
            return $this->insert($order);
        }
        return $this->update($order);
    }

    /**
     * Delete {@link Order} by identifier.
     * @param int $id {@link Order} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM orders 
            WHERE
                id = :id';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            ':id' => $id
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

    private function getFindSql(OrderSearchCriteria $search = null) {
        $sql = 'SELECT o.id, o.customer_id, o.item_id, c.last_name, c.first_name,
                        o.start_date, o.end_date, o.n_weekly, o.daily_skip,
                        o.frequency, o.day_of_week, o.pickup_location_id, o.pickup_time,
                        o.quantity, o.order_date, o.user_id
                        FROM orders o, customers c
                        WHERE o.customer_id = c.id ';
        if ( $search && $search->hasFilter() ) {
            if ( $search->getCustomerId() ) {
                $sql .= ' AND o.customer_id = ' . $search->getCustomerId();
            }
        }
        $sql .= ' ORDER BY c.last_name, c.first_name';
        return $sql;
    }

    /**
     * @return Order
     * @throws Exception
     */
    private function insert(Order $order) {
        $order->setId( null );
        $sql = 'INSERT INTO orders (id, customer_id, item_id, start_date, end_date,
                        frequency, n_weekly, daily_skip, day_of_week, 
                        pickup_location_id, pickup_time, quantity, order_date, user_id)
                VALUES (:id, :customer_id, :item_id, :start_date, :end_date,
                        :frequency, :n_weekly, :daily_skip, :day_of_week, 
                        :pickup_location_id, :pickup_time, :quantity, :order_date, :user_id)';
        $result = $this->execute($sql, $order, self::ORDER_INSERT);
       
        return $result;
    }

    /**
     * @return Order
     * @throws Exception
     */
    private function update(Order $order) {
        $sql = 
            'UPDATE orders SET
                customer_id = :customer_id,
                item_id = :item_id,
                start_date = :start_date,
                end_date = :end_date,
                frequency = :frequency,
                n_weekly = :n_weekly,
                daily_skip = :daily_skip,
                day_of_week = :day_of_week,
                pickup_location_id = :pickup_location_id,
                pickup_time = :pickup_time,
                quantity = :quantity,
                order_date = :order_date,
                user_id = :user_id
            WHERE
                id = :id';
        return $this->execute($sql, $order, self::ORDER_INSERT);
    }

    /**
     * @return Order
     * @throws Exception
     */
    private function execute($sql, Order $order, $query_type) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($order, $query_type));
        if (!$order->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        
        if (!$statement->rowCount()) {
            throw new NotFoundException('Order with ID "' . $order->getId() . '" does not exist.');
        }
        
        return $order;
    }

    private function getParams(Order $order, $query_type ) {
        $params = array();
        switch( $query_type ) {
            case self::ORDER_INSERT:
                $params = array(
                    ':id' => $order->getId(),
                    ':customer_id' => $order->getCustomerId(),
                    ':start_date' => self::formatDateTime($order->getStartDate()),
                    ':end_date' => self::formatDateTime($order->getEndDate()),
                    ':frequency' => $order->getFrequency(),
                    ':n_weekly' => $order->getNWeekly(),
                    ':daily_skip' => $order->getDailySkip(),
                    ':day_of_week' => $order->getDayOfWeek(),
                    ':pickup_location_id' => $order->getLocationId(),
                    ':pickup_time' => $order->getPickupTime()->format( 'H:i' ),
                    ':item_id' => $order->getItemId(),
                    ':quantity' => $order->getQuantity(),
                    ':order_date' => self::formatDateTime(new DateTime()),
                    ':user_id' => Utils::getUserIdByName()
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
