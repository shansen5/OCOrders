<?php

$location = Utils::getLocationByGetId();

$dao = new LocationDao();
$dao->delete($location->getId());
Flash::addFlash('Location deleted successfully.');

Utils::redirect('location-list', array());
