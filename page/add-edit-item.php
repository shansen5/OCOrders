<?php

$errors = array();
$item = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $item = Utils::getItemByGetId();
} else {
    // set defaults
    $item = new Item();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $item->getId() ) {
        Utils::redirect('item-detail', array('id' => $item->getId()));
    } else {
        Utils::redirect('item-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    // for security reasons, do not map the whole $_POST['item']
    $data = array(
        'item_type' => $_POST['item']['type'],
        'sub_type' => $_POST['item']['sub_type'],
        'size' => $_POST['item']['size'],
        'unit' => $_POST['item']['unit'],
    );
        ;
    // map
    ItemMapper::map($item, $data);
    // validate
    $errors = ItemValidator::validate($item);
    // validate
    if (empty($errors)) {
        // save
        $dao = new ItemDao();
        $item = $dao->save($item);
        Flash::addFlash('Item saved successfully.');
        // redirect
        Utils::redirect('item-detail', array('id' => $item->getId()));
    }
}
