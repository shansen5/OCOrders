<?php

$errors = array();
$customer = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $customer = Utils::getCustomerByGetId();
} else {
    // set defaults
    $customer = new Customer();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $customer->getId() ) {
        Utils::redirect('customer-detail', array('id' => $customer->getId()));
    } else {
        Utils::redirect('customer-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    $data = array(
        'first_name' => $_POST['customer']['first_name'],
        'last_name' => $_POST['customer']['last_name'],
        'phone' => $_POST['customer']['phone'],
        'email' => $_POST['customer']['email'],
        'street1' => $_POST['customer']['street1'],
        'street2' => $_POST['customer']['street2'],
        'city' => $_POST['customer']['city'],
        'state' => $_POST['customer']['state'],
        'country' => $_POST['customer']['country'],
        'postal_code' => $_POST['customer']['postal_code'],
    );
        ;
    // map
    CustomerMapper::map($customer, $data);
    // validate
    $errors = CustomerValidator::validate($customer);
    // validate
    if (empty($errors)) {
        // save
        $dao = new CustomerDao();
        $customer = $dao->save($customer);
        Flash::addFlash('Customer saved successfully.');
        // redirect
        Utils::redirect('customer-detail', array('id' => $customer->getId()));
    }
}
