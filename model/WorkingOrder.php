<?php

/**
 * Model class representing one WorkingOrder item.
 */
final class WorkingOrder {


    /** @var int */
    private $id;
    /** @var int */
    private $order_id;
    /** @var int */
    private $pickup_location_id;
    /** @var int */
    private $item_id;
    /** @var DateTime */
    private $delivery_date;
    /** @var DateTime */
    private $modified_date;
    /** @var int */
    private $quantity;
    /** @var string */
    private $user_id;
    
    /** @var Location */
    private $pickup_location;
    /** @var Customer */
    private $customer;
    /** @var Item */
    private $item;

    /**
     * 
     * Create new {@link WorkingOrder} from the Order
     */
    public function __construct( $order ) {
        if ( $order ) {
            $this->setOrderId( $order->getId());
            $this->setItemId( $order->getItemId() );
            $this->setLocationId( $order->getLocationId());
            $this->setQuantity( $order->getQuantity());            
        }
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
            throw new Exception('Cannot change working order id to ' . $id . ', already set to ' . $this->id);
        }
        $this->id = (int) $id;
    }

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($id) {
        if ($this->user_id !== null && $this->user_id != $id) {
            throw new Exception('Cannot change user id to ' . $id . ', already set to ' . $this->id);
        }
        $this->user_id = (int) $id;
    }

        /**
     * @return int <i>null</i> if not persistent
     */
    public function getOrderId() {
        return $this->order_id;
    }

    public function setOrderId($id) {
        $this->order_id = (int) $id;
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
    public function getDeliveryDate() {
        return $this->delivery_date;
    }

    public function setDeliveryDate(DateTime $delivery_date) {
        $this->delivery_date = $delivery_date;
    }

    /**
     * @return DateTime
     */
    public function getModifiedDate() {
        return $this->modified_date;
    }

    public function setModifiedDate(DateTime $modified_date) {
        $this->modified_date = $modified_date;
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
            $dao = new OrderDao();
            $this->customer = $dao->findById( $this->order_id )->getCustomer();
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
     * @return Customer's id
     */
    public function getCustomerId() {
        if ( $this->getCustomer() != null ) {
            return $this->customer->getId();
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
     * @return string
     */
    public function getUser() {
        // Todo
        return $this->user_id;
    }

}
