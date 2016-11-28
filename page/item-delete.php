<?php

$item = Utils::getItemByGetId();

$dao = new ItemDao();
$dao->delete($item->getId());
Flash::addFlash('Item deleted successfully.');

Utils::redirect('item-list', array());
