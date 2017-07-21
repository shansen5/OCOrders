<?php

/**
 * Model class representing one account.
 */
final class Account {

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $default_customer_id;

    /**
     * Create new {@link Account} with default properties set.
     */
    public function __construct() {
        $this->setDefaultCustomerId(0);
    }

    //~ Getters & setters

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = (int) $id;
    }

    /**
     * @return string <i>null</i> if not persistent
     */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

        /**
     * @return int <i>null</i> if not persistent
     */
    public function getDefaultCustomerId() {
        return $this->default_customer_id;
    }

    public function setDefaultCustomerId($id) {
        $this->default_customer_id = (int) $id;
    }

}
