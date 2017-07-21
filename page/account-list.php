<?php

$dao = new AccountDao();

// data for template
$title = 'Accounts';
$accounts = $dao->find();
