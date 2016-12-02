<?php

$order = Utils::getOrderByGetId();

$workingDao = new WorkingOrderDao();
$workingDao->deleteOrder($order);
$dao = new OrderDao();
$dao->delete($order->getId());
Flash::addFlash('Order deleted successfully.');

Utils::redirect('order-list', array());
