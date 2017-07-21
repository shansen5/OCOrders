<?php

/**
 * Mapper for {@link WorkingOrder} from array.
 * @see WorkingOrderValidator
 */
final class WorkingOrderMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link WorkingOrder}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>pickup_location_id</li>
     *   <li>delivery_date</li>
     *   <li>delivery_time</li>
     *   <li>modified_date</li>
     *   <li>user_id</li>
     *   <li>quantity</li>
     * </ul>
     * @param WorkingOrder $order
     * @param array $properties
     */
    public static function map(WorkingOrder $order, array $properties) {
        if (array_key_exists('id', $properties)) {
            $order->setId($properties['id']);
        }
        if (array_key_exists('order_id', $properties)) {
            $order->setOrderId($properties['order_id']);
        }
        if (array_key_exists('item_id', $properties)) {
            $order->setItemId($properties['item_id']);
        }
        if (array_key_exists('pickup_location_id', $properties)) {
            $order->setLocationId($properties['pickup_location_id']);
        }
        if (array_key_exists('user_id', $properties)) {
            $order->setUserId($properties['user_id']);
        }
        if (array_key_exists('delivery_date', $properties)) {
            $deliveryDate = self::createDateTime($properties['delivery_date']);
            if ($deliveryDate) {
                $order->setDeliveryDate($deliveryDate);
            }
        }
        if (array_key_exists('delivery_time', $properties)) {
            $deliveryTime = DateTime::createFromFormat('H:i:s', $properties['delivery_time']);
            if ( !$deliveryTime ) {
               $deliveryTime = DateTime::createFromFormat('H:i', $properties['delivery_time']); 
            }
            if ($deliveryTime) {
                $order->setDeliveryTime($deliveryTime);
            }
        }
        if (array_key_exists('modified_date', $properties)) {
            $modifiedDate = DateTime::createFromFormat('Y-m-d H:i:s',$properties['modified_date']);
            if ($modifiedDate) {
                $order->setModifiedDate($modifiedDate);
            }
        }
        if (array_key_exists('quantity', $properties)) {
            $order->setQuantity($properties['quantity']);
        }
    }

    private static function createDateTime($input) {
        $d = DateTime::createFromFormat('Y-m-d', $input);
        return $d;
    }

}
