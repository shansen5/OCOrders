<?php

/**
 * Model class representing one Order item.
 */
final class Order {


    /** @var int */
    private $id;
    /** @var int */
    private $customer_id;
    /** @var int */
    private $pickup_location_id;
    /** @var int */
    private $item_id;
    /** @var DateTime */
    private $start_date;
    /** @var DateTime */
    private $end_date;
    /** @var int */
    private $frequency;
    /** @var int */
    private $day_of_week;
    /** @var int */
    private $quantity;
    /** @var DateTime */
    private $order_date;
    /** @var int */
    private $user_id;
    
    /** @var Location */
    private $pickup_location;
    /** @var Customer */
    private $customer;
    /** @var Item */
    private $item;

    public static function allDays() {
        return array(
            "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
        );
    }

    public static function allFrequencies() {
        return array( 'DAILY', 'WEEKLY', 'BI-WEEKLY', 'MONTHLY', 'ONCE');
    }
    /**
     * Create new {@link Order} with default properties set.
     */
    public function __construct() {
        $now = new DateTime();
        $this->setStartDate($now);
        $this->setEndDate($now);
    }

    //~ Getters & setters

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        if ($this->id !== null && $this->id != $id) {
            throw new Exception('Cannot change order id to ' . $id . ', already set to ' . $this->id);
        }
        $this->id = (int) $id;
    }

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getCustomerId() {
        return $this->customer_id;
    }

    public function setCustomerId($id) {
        if ($this->customer_id !== null && $this->customer_id != $id) {
            throw new Exception('Cannot change customer id to ' . $id . ', already set to ' . $this->customer_id);
        }
        $this->customer_id = (int) $id;
    }

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getItemId() {
        return $this->item_id;
    }

    public function setItemId($id) {
        $this->item_id = (int) $id;
    }

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getLocationId() {
        return $this->pickup_location_id;
    }

    public function setLocationId($id) {
        $this->pickup_location_id = (int) $id;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->start_date;
    }

    public function setStartDate(DateTime $start_date) {
        $this->start_date = $start_date;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->end_date;
    }

    public function setEndDate(DateTime $end_date) {
        $this->end_date = $end_date;
    }

    /**
     * @return DateTime
     */
    public function getNextDate() {
        /* 
         * Use $frequency, $day_of_week, $start_date, $end_date and the current
         * date to compute the next date for this order.
         */
        return $this->start_date;
    }

    /**
     * @return int
     */
    public function getFrequency() {
        return $this->frequency;
    }

    public function setFrequency($frequency) {
        $this->frequency = $frequency;
    }

    /**
     * @return int
     */
    public function getDayOfWeek() {
        return $this->day_of_week;
    }

    public function setDayOfWeek($day_of_week) {
        $this->day_of_week = $day_of_week;
    }

    /**
     * @return Customer
     */
    public function getCustomer() {
        /*
         * If $customer is null, use the $customer_id to ask for it from 
         * CustomerDao.
         */
        if ($this->customer == null ) {
            $dao = new CustomerDao();
            $this->customer = $dao->findById( $this->customer_id );
        }
        return $this->customer;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
    }
    
    /**
     * @return string
     */
    public function getCustomerName() {
        /*
         * If $customer is null, call getCustomer().
         * CustomerDao.
         */
        if ( $this->getCustomer() != null ) {
            return $this->customer->getLastName() . ', ' . $this->customer->getFirstName();;
        }
        return null;
    }

    /**
     * @return Item
     */
    public function getItem() {
        /*
         * If $item is null, use the $item_id to ask for it from 
         * ItemDao.
         */
        if ($this->item == null ) {
            $dao = new ItemDao();
            $this->item = $dao->findById( $this->item_id );
        }
        return $this->item;
    }

    public function setItem($item) {
        $this->customer = $item;
    }
    
    /**
     * @return Location
     */
    public function getLocation() {
        /*
         * If $pickup_location is null, use the $pickup_location_id to ask for it from 
         * LocationDao.
         */
        if ($this->pickup_location == null ) {
            $dao = new LocationDao();
            $this->pickup_location = $dao->findById( $this->pickup_location_id );
        }
        return $this->pickup_location;
    }

    public function setLocation($pickup_location) {
        $this->customer = $pickup_location;
    }
    
    /**
     * @return string
     */
    public function getLocationName() {
        /*
         * If $customer is null, call getLocation().
         * LocationDao.
         */
        if ( $this->getLocation() != null ) {
            return $this->getLocation()->getName();
        }
        return null;
    }
    /**
     * @return int 
     */
    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($num) {
        $this->quantity = (int) $num;
    }

    /**
     * @return DateTime
     */
    public function getOrderDate() {
        return $this->order_date;
    }

    public function setOrderDate(DateTime $order_date) {
        $this->order_date = $order_date;
    }

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($id) {
        $this->user_id = (int) $id;
    }
    
    
}