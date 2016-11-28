<?php

$errors = array();
$order = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $order = Utils::getWorkingOrderByGetId();
} else {
    // set defaults
    $order = new Order();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    Utils::redirect('detail', array('id' => $order->getId()));
} elseif (array_key_exists('save', $_POST)) {
    $data = array(
        'customer_id' => $_POST['order']['customer_id'],
        'order_id' => $_POST['order']['order_id'],
        'item_id' => $_POST['order']['item_id'],
        'pickup_location_id' => $_POST['order']['pickup_location_id'],
        'delivery_date' => $_POST['order']['delivery_date'],
        'quantity' => $_POST['order']['quantity'],
        );
        ;
    // map
    WorkingOrderMapper::map($order, $data);
    // validate
    $errors = WorkingOrderValidator::validate($order);
    // validate
    if (empty($errors)) {
        // save
        $dao = new WorkingOrderDao();
        $order = $dao->save($order);
        
        Flash::addFlash('Working Order saved successfully.');
        // redirect
        Utils::redirect('working-order-detail', array('id' => $order->getId()));
    }
}
