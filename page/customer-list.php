<?php

$dao = new CustomerDao();

// data for template
$title = 'Customers';
$customers = $dao->find();
