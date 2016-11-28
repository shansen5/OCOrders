<?php

/**
 * Mapper for {@link Item} from array.
 * @see ItemValidator
 */
final class ItemMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Item}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>item_type</li>
     *   <li>sub_type</li>
     *   <li>size</li>
     *   <li>unit</li>
     * </ul>
     * @param Item $item
     * @param array $properties
     */
    public static function map(Item $item, array $properties) {
        if (array_key_exists('id', $properties)) {
            $item->setId($properties['id']);
        }
        if (array_key_exists('item_type', $properties)) {
            $item->setType($properties['item_type']);
        }
        if (array_key_exists('sub_type', $properties)) {
            $item->setSubType($properties['sub_type']);
        }
        if (array_key_exists('size', $properties)) {
            $item->setSize($properties['size']);
        }
        if (array_key_exists('unit', $properties)) {
            $item->setUnit($properties['unit']);
        }
    }

}
