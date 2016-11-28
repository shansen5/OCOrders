<?php

$dao = new OrderDao();

// data for template
$title = 'Orders';
$orders = $dao->find();
