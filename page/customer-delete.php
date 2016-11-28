<?php

$customer = Utils::getCustomerByGetId();

$dao = new CustomerDao();
$dao->delete($customer->getId());
Flash::addFlash('Customer deleted successfully.');

Utils::redirect('customer-list', array());
