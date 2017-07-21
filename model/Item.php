<?php

/**
 * Model class representing one item.
 */
final class Item {

    const UNIT_POUND = "lb";
    const UNIT_OUNCE = "oz";
    const UNIT_GRAM = "g";
    const UNIT_GALLON = "gal";
    const UNIT_QUART = "qt";
    const UNIT_EACH = "ea";
    const UNIT_OTHER = "lb";
    
    
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $code;
    /** @var number */
    private $size;
    /** @var enum */
    private $unit;


    /**
     * Create new {@link Item} with default properties set.
     */
    public function __construct() {
    }

    public static function allUnits() {
        return array(
            self::UNIT_POUND,
            self::UNIT_OUNCE,
            self::UNIT_GRAM,
            self::UNIT_GALLON,
            self::UNIT_QUART,
            self::UNIT_EACH,
            self::UNIT_OTHER,
        );
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
    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
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
     * @return float <i>null</i> if not persistent
     */
    public function getSize() {
        return $this->size;
    }

    public function setSize($sz) {
        $this->size = $sz;
    }

    /**
     * @return string <i>null</i> if not persistent
     */
    public function getUnit() {
        return $this->unit;
    }

    public function setUnit($unit) {
        ItemValidator::validateUnit($unit);
        $this->unit = $unit;
    }

}
