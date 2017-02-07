<?php

$order = Utils::getOrderByGetId();

// Terminate the order by setting the current date as its end date.
// WorkingOrder::saveOrder() will delete all working orders later than
// this date.
$order->setEndDate( new DateTime() );
$workingDao = new WorkingOrderDao();
$workingDao->saveOrder($order);
Flash::addFlash('Order terminated successfully.');

Utils::redirect('order-list', array());
