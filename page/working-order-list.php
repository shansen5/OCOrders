<?php

$title = 'Working Orders';

if ( !isset( $_POST['start_date'])) {
    $aday = new DateTime();
    $_POST['start_date'] = Utils::formatDate( $aday );
    $dayInterval = new DateInterval( "P1D" );
    $aday->add( $dayInterval );
    $_POST['end_date'] = Utils::formatDate( $aday );
}
$dao = new WorkingOrderDao();
$search = new WorkingOrderSearchCriteria();
$search->setCustomerId( $_POST['customer_id'] );
$search->setItemId( $_POST['item_id'] );
$search->setLocationId( $_POST['pickup_location_id'] );
$search->setStartDate( $_POST['start_date'] );
$search->setEndDate( $_POST['end_date'] );

$working_orders = $dao->find( $search );

if (array_key_exists('download_all', $_POST)) {
    download_all( $working_orders );
}

if (array_key_exists('download_types', $_POST)) {
    download_by_types( $working_orders );
}



function download_by_types( $working_orders ) {
    /**
     * Sort the orders by types.
     * List the orders, with a summation of the number of that type
     */
    $dir = getcwd();
    $filename = 'logs/oc_orders_types.csv';
    $handle = fopen( $filename, 'w' );
    if ( $handle ) {
        fwrite( $handle, "Working Orders by Type of Item\n" );
        fwrite( $handle, "Start:, " . $_POST['start_date'] . ", End:, " . $_POST['end_date'] . "\n" );

        fwrite( $handle, "Delivery Date, Item Type, Subtype, Size, Unit, Quantity\n" );
        usort( $working_orders, function( WorkingOrder $a, WorkingOrder $b )
        {
            if ( $a->getDeliveryDate() > $b->getDeliveryDate() ) {
                return 1;
            } else if ( $a->getDeliveryDate() < $b->getDeliveryDate() ) {
                return -1;
            } else {  // Same date
                if ( $a->getItemId() == $b->getItemId() ) {
                    return 0;
                } else if ( $a->getItemId() < $b->getItemId() ) {
                    return -1;
                } else {
                    return 1;
                }
            }
        });
        $total_sum = 0;
        $date_sum = 0;
        $total_item_sum = array();
        $item_sum = 0;
        $day = null;
        $item_id = 0;
        $it = null;
        $item_key = null;
        foreach ($working_orders as $working_order) {
            $it = $working_order->getItem();
            if ( $day ) {
                if ( $working_order->getDeliveryDate() != $day ) {
                    // New day, report the previous one
                    $day_sum += $item_sum;
                    $total_item_sum[$item_key] += $item_sum; 
                    report_item( $handle, $item_key, $item_sum );
                    fwrite( $handle, ',,Day total,,,' . $day_sum . "\n" );
                    // Reset counters for item and day
                    $item_sum = $working_order->getQuantity();
                    $day_sum = 0;
                    // Get new item and day
                    $item_id = $working_order->getItemId();
                    $item_key = make_item_key( $working_order->getItem() );
                    $day = $working_order->getDeliveryDate();
                    // Print new day
                    fwrite( $handle, Utils::formatDate( $day ) . "\n" );
                } else { // $day is the same as previous
                    if ( $working_order->getItemId() != $item_id ) {
                        // New item, report the previous one.
                        $day_sum += $item_sum;
                        $total_item_sum[$item_key] += $item_sum; 
                        report_item( $handle, $item_key, $item_sum );
                        // Reset counter for item
                        $item_sum = $working_order->getQuantity();
                        $item_id = $working_order->getItemId();
                        $item_key = make_item_key( $working_order->getItem() );
                    } else {  // Item and day are the same.
                        $item_sum += $working_order->getQuantity();
                    }
                }
            } else {  // $day is null -- First day
                $day = $working_order->getDeliveryDate();
                fwrite( $handle, Utils::formatDate( $day ) . "\n" );
                $item_id = $working_order->getItemId();
                $item_key = make_item_key( $working_order->getItem() );
                $item_sum = $working_order->getQuantity();
            }
        }
        if ( $it ) {
            $day_sum += $item_sum;
            $total_item_sum[$item_key] += $item_sum; 
            report_item( $handle, make_item_key( $it ), $item_sum );
            fwrite( $handle, ',,Day total,,,' . $day_sum . "\n" );
        }
        fwrite( $handle, "\nTotals by item\n" );
        while ( list( $key, $val ) = each( $total_item_sum )) {
            report_item( $handle, $key, $val );
        }
        fclose( $handle );
        make_header( $filename );
    }
}

function report_item( $handle, $key, $sum ) {
    fwrite( $handle, ',' . $key  . ', ' . $sum . "\n");
}    

function make_item_key( Item $it ) {
    return $it->getType() . ', ' . $it->getSubtype() . ', '
        . $it->getSize() . ', ' . $it->getUnit();
}

function download_all( $working_orders ) {
    $dir = getcwd();
    $filename = 'logs/oc_orders.csv';
    $handle = fopen( $filename, 'w' );
    if ( $handle ) {
        fwrite( $handle, "Working Orders by Filter\n" );
        fwrite( $handle, "Start:, " . $_POST['start_date'] . ", End:, " . $_POST['end_date'] . "\n" );
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
        make_header( $filename );
    }
}

function make_header( $filename ) {
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
    unlink( $filename );
}


