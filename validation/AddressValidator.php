<?php

/**
 * Validator for {@link Location}.
 * @see LocationMapper
 */
final class AddressValidator {

    private function __construct() {
    }

    /**
     * Validate the given state.
     * @param string $state state to be validated
     * @throws Exception if the state is not valid
     */
    public static function validateState($state) {
    }

    /**
     * Validate the given country.
     * @param string $country country to be validated
     * @throws Exception if the country is not valid
     */
    public static function validateCountry($country) {
    }

    /**
     * Validate the given postal_code.
     * @param string $postal_code postal_code to be validated
     * @throws Exception if the postal_code is not valid
     */
    public static function validatePostalCode($postal_code) {
    }

}
