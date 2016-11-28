<?php

$title = 'Working Orders';

$dao = new WorkingOrderDao();
$search = new WorkingOrderSearchCriteria();
$search->setCustomerId( $_POST['customer_id'] );
$search->setItemId( $_POST['item_id'] );
$search->setLocationId( $_POST['pickup_location_id'] );
$search->setStartDate( $_POST['start_date'] );
$search->setEndDate( $_POST['end_date'] );

$working_orders = $dao->find( $search );

if (array_key_exists('download', $_POST)) {
    $dir = getcwd();
    $filename = 'logs/oc_orders.csv';
    $handle = fopen( $filename, 'w' );
    if ( $handle ) {
        fwrite( $handle, "Item Type, Subtype, Size, Unit, Delivery Date, Location, Quantity, Customer Last, Customer First\n" );
        foreach ($working_orders as $working_order) {
            $it = $working_order->getItem();
            if ( $it ) {
                fwrite( $handle, $it->getType() . ', ' . $it->getSubtype() . ', '
                    . $it->getSize() . ', ' . $it->getUnit() . ', '
                    . Utils::formatDate( $working_order->getDeliveryDate() ) . ', '
                    . $working_order->getLocationName() . ', '
                    . $working_order->getQuantity() . ', '
                    . $working_order->getCustomerName() . "\n" );
            } else {
                fwrite( $handle, 'null, null, null, null, '
                    . Utils::formatDate( $working_order->getDeliveryDate() ) . ', '
                    . $working_order->getLocationName() . ', '
                    . $working_order->getQuantity() . ', '
                    . $working_order->getCustomerName() . "\n" );
                
            }
        }
        fclose( $handle );
        if (file_exists($filename)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/text');
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
            // exit;
        }
        $did = unlink( $filename );
    }

}


