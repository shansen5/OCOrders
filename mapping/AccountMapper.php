<?php

/**
 * Mapper for {@link Account} from array.
 * @see AccountValidator
 */
final class AccountMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Account}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>name</li>
     * </ul>
     * @param Account $account
     * @param array $properties
     */
    public static function map(Account $account, array $properties) {
        if (array_key_exists('id', $properties)) {
            $account->setId($properties['id']);
        }
        if (array_key_exists('name', $properties)) {
            $account->setName($properties['name']);
        }
        if (array_key_exists('default_customer_id', $properties)) {
            $account->setDefaultCustomerId( $properties['default_customer_id'] );
        }
    }

}
