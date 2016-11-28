<?php

$errors = array();
$order = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $order = Utils::getOrderByGetId();
} else {
    // set defaults
    $order = new Order();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $order->getId() ) {
        Utils::redirect('order-detail', array('id' => $order->getId()));
    } else {
        Utils::redirect('order-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    $data = array(
        'customer_id' => $_POST['order']['customer_id'],
        'item_id' => $_POST['order']['item_id'],
        'pickup_location_id' => $_POST['order']['pickup_location_id'],
        'start_date' => $_POST['order']['start_date'],
        'end_date' => $_POST['order']['end_date'],
        'frequency' => $_POST['order']['frequency'],
        'quantity' => $_POST['order']['quantity'],
        'day_of_week' => $_POST['order']['day_of_week']
        );
        ;
    // map
    OrderMapper::map($order, $data);
    // validate
    $errors = OrderValidator::validate($order);
    // validate
    if (empty($errors)) {
        // save
        $dao = new OrderDao();
        $order = $dao->save($order);
        $workingDao = new WorkingOrderDao();
        $workingDao->saveOrder($order);
        
        Flash::addFlash('Order saved successfully.');
        // redirect
        Utils::redirect('order-detail', array('id' => $order->getId()));
    }
}
