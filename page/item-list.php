<?php

$dao = new ItemDao();

// data for template
$title = 'Items';
$items = $dao->find();
