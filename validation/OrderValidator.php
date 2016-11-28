<?php

/**
 * Validator for {@link Order}.
 * @see OrderMapper
 */
final class OrderValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Order} instance.
     * @param Order $order {@link Order} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(Order $order) {
        $errors = array();
        if (!$order->getStartDate()) {
            $errors[] = new Error('start_date', 'Start date cannot be empty.');
        }
        return $errors;
    }

}
