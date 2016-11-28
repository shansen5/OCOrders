<?php

$order = Utils::getOrderByGetId();

$dao = new OrderDao();
$dao->delete($order->getId());
Flash::addFlash('Order deleted successfully.');

Utils::redirect('order-list', array());
