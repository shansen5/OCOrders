<?php

/**
 * Validator for {@link Customer}.
 * @see CustomerMapper
 */
final class CustomerValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Customer} instance.
     * @param Customer $customer {@link Customer} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(Customer $customer) {
        $errors = array();
        if (!$customer->getLastName()) {
            $errors[] = new Error('name', 'Last name cannot be empty.');
        }
        return $errors;
    }

}
