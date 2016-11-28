<?php

$dao = new LocationDao();

// data for template
$title = 'Locations';
$locations = $dao->find();
