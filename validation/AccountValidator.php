<?php

/**
 * Validator for {@link Account}.
 * @see AccountMapper
 */
final class AccountValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Account} instance.
     * @param Account $todo {@link Account} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(Account $account) {
        $errors = array();
        if (!trim($account->getName())) {
            $errors[] = new Error('name', 'Name cannot be empty.');
        }
        return $errors;
    }


}
