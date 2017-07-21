<?php

$order = Utils::getWorkingOrderByGetId();

// Don't delete working orders that are in the past.
$today = new DateTime();
if ( $order->getDeliveryDate() < $today ) {
    Flash::addFlash('Cannot delete a past Working Order.');    
} else {
    $dao = new WorkingOrderDao();
    $dao->delete($order->getId());
    Flash::addFlash('WorkingOrder deleted successfully.');  
}

Utils::redirect('working-order-list', array());
