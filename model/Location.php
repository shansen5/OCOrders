<?php

/**
 * Model class representing one Location item.
 */
final class Location {

    const SOUTH_ZONE = 'South';
    const HOME_ZONE = 'Home';
    const NORTH_ZONE = 'North';
    
    /** @var int */
    private $id;
    /** @var int */
    private $address_id;
    /** @var string */
    private $name;
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
    /** @var string */
    private $zone;

    public static function allZones() {
        return array(
            self::NORTH_ZONE,
            self::HOME_ZONE,
            self::SOUTH_ZONE,
        );
    }

    
    /**
     * Create new {@link Location} with default properties set.
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
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
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

    /**
     * @return string
     */
    public function getZone() {
        return $this->zone;
    }

    public function setZone($zone) {
        LocationValidator::validateZone($zone);
        $this->zone = $zone;
    }

}
