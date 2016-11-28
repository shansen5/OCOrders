<?php

/**
 * Model class representing one Customer item.
 */
final class Customer {

    /** @var int */
    private $id;
    /** @var int */
    private $address_id;
    /** @var string */
    private $first_name;
    /** @var string */
    private $last_name;
    /** @var string */
    private $phone;
    /** @var string */
    private $email;
    /** @var string */
    private $street1;
    /** @var string */
    private $street2;
    /** @var string */
    private $city;
    /** @var string */
    private $state;
    /** @var string */
    private $postal_code;
    /** @var string */
    private $country;


    /**
     * Create new {@link Customer} with default properties set.
     */
    public function __construct() {
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
            throw new Exception('Cannot change identifier to ' . $id . ', already set to ' . $this->id);
        }
        $this->id = (int) $id;
    }

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getAddressId() {
        return $this->address_id;
    }

    public function setAddressId($id) {
        if ($this->address_id !== null && $this->address_id != $id) {
            throw new Exception('Cannot change identifier to ' . $id . ', already set to ' . $this->address_id);
        }
        $this->address_id = (int) $id;
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($first_name) {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($last_name) {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getStreet1() {
        return $this->street1;
    }

    public function setStreet1($street1) {
        $this->street1 = $street1;
    }

    /**
     * @return string
     */
    public function getStreet2() {
        return $this->street2;
    }

    public function setStreet2($street2) {
        $this->street2 = $street2;
    }

    /**
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        AddressValidator::validateState($state);
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        AddressValidator::validateCountry($country);
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getPostalCode() {
        return $this->postal_code;
    }

    public function setPostalCode($postal_code) {
        AddressValidator::validatePostalCode($postal_code);
        $this->postal_code = $postal_code;
    }


}
