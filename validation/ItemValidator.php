<?php

/**
 * Validator for {@link Item}.
 * @see ItemMapper
 */
final class ItemValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Todo} instance.
     * @param Todo $todo {@link Todo} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(Item $item) {
        $errors = array();
        if (!trim($item->getCode())) {
            $errors[] = new Error('type', 'Type cannot be empty.');
        }
        if ( !$item->getSize() ) {
            $errors[] = new Error('size', 'Size cannot be empty.');
        } elseif ( $item->getSize() <= 0 ) {
            $errors[] = new Error('size', 'Size must be positive.');
        }
        if (!trim($item->getUnit())) {
            $errors[] = new Error('unit', 'Unit cannot be empty.');
        } elseif (!in_array($item->getUnit(), Item::allUnits())) {
            $errors[] = new Error('unit', 'Invalid Unit.');
        }
        return $errors;
    }

    /**
     * Validate the given unit.
     * @param string $unit unit to be validated
     * @throws Exception if the unit is not known
     */
    public static function validateUnit($unit) {
        if (!in_array($unit, Item::allUnits())) {
            throw new Exception('Unknown unit: ' . $unit);
        }
    }

}
