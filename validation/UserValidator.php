<?php

/**
 * Validator for {@link User}.
 * @see UserMapper
 */
final class UserValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link User} instance.
     * @param User $user {@link User} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(User $user) {
        $errors = array();
        if (!$user->getUsername()) {
            $errors[] = new Error('username', 'Username cannot be empty.');
        }
        if (!$user->getPassword()) {
            $errors[] = new Error('password', 'Password cannot be empty.');
        }
        if (!$user->getRole()) {
            $errors[] = new Error('role', 'Role cannot be empty.');
        }
        return $errors;
    }

}
