<?php

/**
 * Mapper for {@link Location} from array.
 * @see LocationValidator
 */
final class LocationMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Location}.
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
     * @param Location $location
     * @param array $properties
     */
    public static function map(Location $location, array $properties) {
        if (array_key_exists('id', $properties)) {
            $location->setId($properties['id']);
        }
        if (array_key_exists('name', $properties)) {
            $location->setName($properties['name']);
        }
        if (array_key_exists('address_id', $properties)) {
            $location->setAddressId($properties['address_id']);
        }
        if (array_key_exists('street1', $properties)) {
            $location->setStreet1($properties['street1']);
        }
        if (array_key_exists('street2', $properties)) {
            $location->setStreet2($properties['street2']);
        }
        if (array_key_exists('city', $properties)) {
            $location->setCity($properties['city']);
        }
        if (array_key_exists('state', $properties)) {
            $location->setState($properties['state']);
        }
        if (array_key_exists('postal_code', $properties)) {
            $location->setPostalCode($properties['postal_code']);
        }
        if (array_key_exists('country', $properties)) {
            $location->setCountry($properties['country']);
        }
    }

}
