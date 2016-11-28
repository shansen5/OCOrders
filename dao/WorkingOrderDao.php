<?php

/**
 * DAO for {@link WorkingOrder}.
 */
final class WorkingOrderDao {

    const ORDER_INSERT = 1;
    const ORDER_UPDATE = 2;
    
    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link WorkingOrder}s by search criteria.
     * @return array array of {@link WorkingOrder}s
     */
    public function find(WorkingOrderSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $order = new WorkingOrder();
            WorkingOrderMapper::map($order, $row);
            $result[$order->getId()] = $order;
        }
        return $result;
    }

    /**
     * Find {@link WorkingOrder} by identifier.
     * @return WorkingOrder or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT id, order_id, item_id,
                        delivery_date, modified_date, pickup_location_id,
                        user_id, quantity
                        FROM working_orders 
                        WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $order = new WorkingOrder();
        WorkingOrderMapper::map($order, $row);
        return $order;
    }

    /**
     * Create WorkingOrder records from the Order. {@link Order}.
     * @param Order $order {@link Order} to be used
     */
    public function saveOrder($order) {
        /** 
         * Begin with the later of today's date and the start_date of the order.
         * Move to the next delivery_date, based on the order's frequency and
         * day_of_week. Create a working_order record for that date.
         * Continue until the order's end_date is reached.
         * 
         * Frequency behavior.
         *   - ONCE - The order gets one working_order on the start_date.
         *   - WEEKLY - A working_order is created on every day_of_week
         *       between start_date and end_date.
         *   - BI-WEEKLY - A working_order is created on every second day_of_week
         *       between start_date and end_date.
         *   - MONTHLY - A working_order is created each month, starting on
         *      the first day_of_week after the start_date, and the same number
         *      of day_of_week each month until the end_date
         */
        if ( ! $order ) {
            return null;
        }
        if ( $order->getFrequency() == 'MONTHLY' ) {
            // Handle this case separately
            $this->saveMonthlyOrders( $order );
        } else {
            $working_date = new DateTime();
            if ( $working_date < $order->getStartDate() ) {
                $working_date = $order->getStartDate();
            }
            if ( $order->getFrequency() == 'ONCE' ) {
                $new_wo = new WorkingOrder( $order );
                $new_wo->setDeliveryDate( $working_date );
                if ( ! $this->save( $new_wo )) {
                    throw new Exception( 'Failed to save WorkingOrder' . $working_date );
                }
                return;
            }
            // if ( $order->getFrequency() != 'WEEKLY' && $order->getFrequency() != 'BI-WEEKLY' ) {
            //     throw new Exception( 'Only ONCE, WEEKLY and BI-WEEKLY frequencies currently.' );
            // }
            $order_dw = $order->getDayOfWeek();
            $interval7 = new DateInterval( 'P7D' );
            $interval14 = new DateInterval( 'P14D' );
            $intervalMon = new DateInterval( 'P1M' );
            $interval1 = new DateInterval( 'P1D' );
            while ( $working_date < $order->getEndDate() ) {
                if ( $working_date->format( "D" ) == $order_dw ) {
                    $new_wo = new WorkingOrder( $order );
                    $new_wo->setDeliveryDate( $working_date );
                    if ( ! $this->save( $new_wo )) {
                        throw new Exception( 'Failed to save WorkingOrder ' . $working_date );
                    } 
                    if ( $order->getFrequency() == 'WEEKLY' ) {
                        $working_date->add( $interval7 );
                    }
                    if ( $order->getFrequency() == 'BI-WEEKLY' ) {
                        $working_date->add( $interval14 );
                    }
                    if ( $order->getFrequency() == 'MONTHLY' ) {
                        if ( !$foundMonthly1st ) {
                            $foundMonthly1st = true;
                        }
                        $working_date->add( $intervalMon );
                    }

                } else {
                    $working_date->add( $interval1 );
                }
            }

        }
        return $order;
    }


    /**
     * Create WorkingOrder records from the Order. {@link Order}.
     * Special handling of the MONTHLY frequency case.
     * @param Order $order {@link Order} to be used
     */
private function saveMonthlyOrders( Order $order ) {
    /*
     * Find the next day_of_week after the start_date.  Get the number of
     * that day in the month (zero-based).  Add records on that number day_of_week
     * for each month to the end_date. 
     * If the number of the day_of_week is 4 (i.e. 5th of the month) there will
     * be months without a fifth of that day, so we just add on the last day_of_week
     * of each month.
     */
    $start_date = $order->getStartDate();
    $end_date = $order->getEndDate();
    $dow = $order->getDayOfWeek();
    $working_date = $start_date;
    $number_in_month = -1;
    $fifthdow = false;
    $month_todo = $working_date->format( 'm' );
    $intervalMon = new DateInterval( 'P1M' );
    $interval7 = new DateInterval( 'P7D' );
    $interval1 = new DateInterval( 'P1D' );

    while ( $working_date < $end_date ) {
        if ( $working_date->format( "D" ) == $dow ) {
            $month_todo = ( $month_todo + 1 ) % 12;
            $dim = $working_date->format( 'd' );
            $new_number_in_month = intdiv( $dim - 1, 7 );
            if ( $fifthdow && $new_number_in_month == 0 ) {
                // Back up to previous week
                $working_date->sub( $interval1 );
                while ( $working_date->format( 'D') != $dow ) {
                    $working_date->sub( $interval1 );
                }
            }
            if ( $new_number_in_month > $number_in_month ) {
                if ( $number_in_month == -1 ) {
                    $number_in_month = $new_number_in_month;
                    if ( $number_in_month == 4 ) {
                        // Make it the last $dow of each month
                        $fifthdow = true;
                    }
                } else {
                    // Back up to previous week
                    $working_date->sub( $interval1 );
                    while ( $working_date->format( 'D') != $dow ) {
                        $working_date->sub( $interval1 );
                    }
                }
            }
            $new_wo = new WorkingOrder( $order );
            $new_wo->setDeliveryDate( $working_date );
            if ( ! $this->save( $new_wo )) {
                throw new Exception( 'Failed to save WorkingOrder ' . $working_date );
            } 
            $working_date->add( $intervalMon );
            if (( $working_date->format( "m" ) == 1 && $month_todo == 12 ) ||
                ( $working_date->format( "m" ) > $month_todo )) {
                    $working_date->sub( $interval7 );
            }
        } else {
            $working_date->add( $interval1 );
        }
    }
}

    /**
     * Save {@link WorkingOrder}.
     * @param WorkingOrder $order {@link WorkingOrder} to be saved
     * @return WorkingOrder saved {@link WorkingOrder} instance
     */
    public function save(WorkingOrder $order) {
        if ($order->getId() === null) {
            return $this->insert($order);
        }
        return $this->update($order);
    }

    /**
     * Delete {@link Working} by identifier.
     * @param int $id {@link } identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM working_orders 
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

    private function getFindSql(WorkingOrderSearchCriteria $search = null) {
        $sql = 'SELECT w.id, w.order_id, w.item_id, w.user_id,
                        w.delivery_date, w.modified_date, 
                        w.pickup_location_id, w.quantity, o.customer_id
                        FROM working_orders w, orders o 
                        WHERE w.order_id = o.id ';
        if ( $search && $search->hasFilter() ) {
            if ( $search->getStartDate() ) {
                if ( $search->getEndDate() ) {
                    $sql .= ' AND w.delivery_date >= "' . $search->getStartDate() . '" '
                        . ' AND w.delivery_date <= "' . $search->getEndDate() . '" ';
                } else {
                    // If start_date was set, an empty end_date means just that one day.   
                    $sql .= ' AND w.delivery_date = "' . $search->getStartDate() . '" ';
                }
            } else {
                if ( $search->getEndDate() ) {
                    // What does empty start_date but end_date set mean?
                    // All rows with delivery_date less than the end_date.
                    $sql .= ' AND w.delivery_date <= "' . $search->getEndDate() . '" ';
                }
            }
            if ( $search->getCustomerId() ) {
                $sql .= ' AND o.customer_id = ' . $search->getCustomerId();
            }
            if ( $search->getItemId() ) {
                $sql .= ' AND w.item_id = ' . $search->getItemId();
            }
            if ( $search->getLocationId() ) {
                $sql .= ' AND w.pickup_location_id = ' . $search->getLocationId();
            }
        }
        
        $sql .= ' ORDER BY delivery_date';
        return $sql;
    }

    /**
     * @return 
     * @throws Exception
     */
    private function insert( $working_order) {
        $working_order->setId( null );
        $sql = 'INSERT INTO working_orders (id, order_id, item_id, pickup_location_id, user_id, 
                    delivery_date, modified_date, quantity)
                VALUES (:id, :order_id, :item_id, :pickup_location_id, :user_id,
                        :delivery_date, :modified_date, :quantity)';
        $result = $this->execute($sql, $working_order, self::ORDER_INSERT);
       
        return $result;
    }

    /**
     * @return 
     * @throws Exception
     */
    private function update( $order) {
        $sql = 
            'UPDATE working_orders SET
                item_id = :item_id,
                pickup_location_id = :pickup_location_id,
                user_id = :user_id,
                delivery_date = :delivery_date,
                modified_date = :modified_date,
                quantity = :quantity
            WHERE
                id = :id';
        return $this->execute($sql, $order, self::ORDER_UPDATE);
    }

    /**
     * @return 
     * @throws Exception
     */
    private function execute($sql,  $order, $query_type) {
        $statement = $this->getDb()->prepare($sql);
        $params = $this->getParams($order, $query_type);
        $this->executeStatement($statement, $params);
        if (!$order->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        
        if (!$statement->rowCount()) {
            throw new NotFoundException(' with ID "' . $order->getId() . '" does not exist.');
        }
        
        return $order;
    }

    private function getParams( $order, $query_type ) {
        $params = array(
            ':item_id' => $order->getItemId(),
            ':pickup_location_id' => $order->getLocationId(),
            ':delivery_date' => self::formatDateTime($order->getDeliveryDate()),
            ':modified_date' => self::formatDateTime(new DateTime()),
            ':quantity' => $order->getQuantity(),
            ':user_id' => Utils::getUserIdByName( $_SESSION['oc_user'] ),
            ':id' => $order->getId()
            );
        switch( $query_type ) {
            case self::ORDER_INSERT:
                $params['order_id'] = $order->getOrderId();
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
