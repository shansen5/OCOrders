<?php

/**
 * Validator for {@link WorkingOrder}.
 * @see WorkingOrderMapper
 */
final class WorkingOrderValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link WorkingOrder} instance.
     * @param WorkingOrder $working_order {@link WorkingOrder} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(WorkingOrder $working_order) {
        $errors = array();
        if (!$working_order->getDeliveryDate()) {
            $errors[] = new Error('delivery_date', 'Delivery date cannot be empty.');
        }
        return $errors;
    }

}
