<?php

/**
 * Mapper for {@link Customer} from array.
 * @see CustomerValidator
 */
final class CustomerMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Customer}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>name</li>
     *   <li>address_id</li>
     *   <li>street1</li>
     *   <li>street2</li>
     *   <li>city</li>
     *   <li>state</li>
     *   <li>postal_code</li>
     *   <li>country</li>
     * </ul>
     * @param Customer $customer
     * @param array $properties
     */
    public static function map(Customer $customer, array $properties) {
        if (array_key_exists('id', $properties)) {
            $customer->setId($properties['id']);
        }
        if (array_key_exists('first_name', $properties)) {
            $customer->setFirstName($properties['first_name']);
        }
        if (array_key_exists('last_name', $properties)) {
            $customer->setLastName($properties['last_name']);
        }
        if (array_key_exists('phone', $properties)) {
            $customer->setPhone($properties['phone']);
        }
        if (array_key_exists('email', $properties)) {
            $customer->setEmail($properties['email']);
        }
        if (array_key_exists('address_id', $properties)) {
            $customer->setAddressId($properties['address_id']);
        }
        if (array_key_exists('street1', $properties)) {
            $customer->setStreet1($properties['street1']);
        }
        if (array_key_exists('street2', $properties)) {
            $customer->setStreet2($properties['street2']);
        }
        if (array_key_exists('city', $properties)) {
            $customer->setCity($properties['city']);
        }
        if (array_key_exists('state', $properties)) {
            $customer->setState($properties['state']);
        }
        if (array_key_exists('postal_code', $properties)) {
            $customer->setPostalCode($properties['postal_code']);
        }
        if (array_key_exists('country', $properties)) {
            $customer->setCountry($properties['country']);
        }
    }

}
