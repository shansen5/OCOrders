<?php

$order = Utils::getWorkingOrderByGetId();

$dao = new WorkingOrderDao();
$dao->delete($order->getId());
Flash::addFlash('WorkingOrder deleted successfully.');

Utils::redirect('working-order-list', array());
