<?php

$errors = array();
$account = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $account = Utils::getAccountByGetId();
} else {
    // set defaults
    $account = new Account();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $account->getId() ) {
        Utils::redirect('account-detail', array('id' => $account->getId()));
    } else {
        Utils::redirect('account-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    // for security reasons, do not map the whole $_POST['account']
    $data = array(
        'name' => $_POST['account']['name'],
        'default_customer_id' => $_POST['account']['default_customer_id'],
    );
    // map
    AccountMapper::map($account, $data);
    // validate
    $errors = AccountValidator::validate($account);
    // validate
    if (empty($errors)) {
        // save
        $dao = new AccountDao();
        $account = $dao->save($account);
        Flash::addFlash('Account saved successfully.');
        // redirect
        Utils::redirect('account-detail', array('id' => $account->getId()));
    }
}
