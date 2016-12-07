<?php

$errors = array();
$order = null;
$edit = array_key_exists('id', $_GET);
$clone = array_key_exists('clone', $_GET );
if ($edit) {
    $order = Utils::getOrderByGetId();
    if ( $clone ) {
        $order->setId( 0 );
    }
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
    $day_byte = 0;
    foreach( range( 1,7 ) as $idx ) {
        $day_byte += (int)$_POST['dayskip'][$idx] * ( 1 << $idx );
    }
    $data = array(
        'customer_id' => $_POST['order']['customer_id'],
        'item_id' => $_POST['order']['item_id'],
        'pickup_location_id' => $_POST['order']['pickup_location_id'],
        'pickup_time' => $_POST['order']['pickup_time'],
        'start_date' => $_POST['order']['start_date'],
        'end_date' => $_POST['order']['end_date'],
        'frequency' => $_POST['order']['frequency'],
        'n_weekly' => $_POST['order']['n_weekly'],
        'daily_skip' => $day_byte,
        'quantity' => $_POST['order']['quantity'],
        'day_of_week' => $_POST['order']['day_of_week']
        );
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
