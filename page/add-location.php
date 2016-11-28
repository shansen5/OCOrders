<?php

$errors = array();
$location = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $location = Utils::getLocationByGetId();
} else {
    // set defaults
    $location = new Location();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $location->getId() ) {
        Utils::redirect('location-detail', array('id' => $location->getId()));
    } else {
        Utils::redirect('location-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    $data = array(
        'name' => $_POST['location']['name'],
        'street1' => $_POST['location']['street1'],
        'street2' => $_POST['location']['street2'],
        'city' => $_POST['location']['city'],
        'state' => $_POST['location']['state'],
        'country' => $_POST['location']['country'],
        'postal_code' => $_POST['location']['postal_code'],
    );
        ;
    // map
    LocationMapper::map($location, $data);
    // validate
    $errors = LocationValidator::validate($location);
    // validate
    if (empty($errors)) {
        // save
        $dao = new LocationDao();
        $location = $dao->save($location);
        Flash::addFlash('Location saved successfully.');
        // redirect
        Utils::redirect('location-detail', array('id' => $location->getId()));
    }
}
