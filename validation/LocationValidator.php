<?php

/**
 * Validator for {@link Location}.
 * @see LocationMapper
 */
final class LocationValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Location} instance.
     * @param Location $location {@link Location} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(Location $location) {
        $errors = array();
        if (!$location->getName()) {
            $errors[] = new Error('name', 'Name cannot be empty.');
        }
        return $errors;
    }

        /**
     * Validate the given zone.
     * @param string $zone to be validated
     * @throws Exception if the zone is not known
     */
    public static function validateZone($zone) {
        if (!in_array($zone, Location::allZones())) {
            throw new Exception('Unknown zone: ' . $zone);
        }
    }

}
