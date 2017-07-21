<?php

/**
 * Mapper for {@link Order} from array.
 * @see OrderValidator
 */
final class OrderMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Order}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>customer_id</li>
     *   <li>pickup_location_id</li>
     *   <li>start_date</li>
     *   <li>end_date</li>
     *   <li>frequency</li>
     *   <li>n_weekly</li>
     *   <li>day_of_week</li>
     *   <li>quantity</li>
     *   <li>order_date</li>
     *   <li>user_id</li>
     * </ul>
     * @param Order $order
     * @param array $properties
     */
    public static function map(Order $order, array $properties) {
        if (array_key_exists('id', $properties)) {
            $order->setId($properties['id']);
        }
        if (array_key_exists('account_id', $properties)) {
            $order->setAccountId($properties['account_id']);
        }
        if (array_key_exists('customer_id', $properties)) {
            $order->setCustomerId($properties['customer_id']);
        }
        if (array_key_exists('item_id', $properties)) {
            $order->setItemId($properties['item_id']);
        }
        if (array_key_exists('pickup_location_id', $properties)) {
            $order->setLocationId($properties['pickup_location_id']);
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
        if (array_key_exists('start_date', $properties)) {
            $startDate = self::createDateTime($properties['start_date']);
            if ($startDate) {
                $order->setStartDate($startDate);
            }
        }
        if (array_key_exists('end_date', $properties)) {
            $endDate = self::createDateTime($properties['end_date']);
            if ($endDate) {
                $order->setEndDate($endDate);
            }
        }
        if (array_key_exists('frequency', $properties)) {
            $order->setFrequency($properties['frequency']);
        }
        if (array_key_exists('n_weekly', $properties)) {
            $order->setNWeekly($properties['n_weekly']);
        }
        if (array_key_exists('daily_skip', $properties)) {
            $order->setDailySkip($properties['daily_skip']);
        }
        if (array_key_exists('day_of_week', $properties)) {
            $order->setDayOfWeek($properties['day_of_week']);
        }
        if (array_key_exists('quantity', $properties)) {
            $order->setQuantity($properties['quantity']);
        }
        if (array_key_exists('order_date', $properties)) {
            $orderDate = DateTime::createFromFormat('Y-m-d H:i:s', $properties['order_date']);
            if ($orderDate) {
                $order->setOrderDate($orderDate);
            }
        }
        if (array_key_exists('user_id', $properties)) {
            $order->setUserId($properties['user_id']);
        }
    }

    private static function createDateTime($input) {
        return DateTime::createFromFormat('Y-m-d', $input);
    }

}
