<?php

$account = Utils::getAccountByGetId();

$dao = new AccountDao();
$dao->delete($account->getId());
Flash::addFlash('Account deleted successfully.');

Utils::redirect('account-list', array());
