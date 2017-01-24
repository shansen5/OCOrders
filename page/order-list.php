<?php

$dao = new OrderDao();
$search = new OrderSearchCriteria();
$search->setCustomerId( $_POST['customer_id'] );
$search->setAccountId( $_POST['account_id'] );

$title = 'Orders';
$orders = $dao->find( $search );
// data for template

if (array_key_exists('download_orders', $_POST)) {
    download_orders( $orders );
}

function download_orders( $orders ) {
    /**
     * Sort the orders by types.
     * List the orders, with a summation of the number of that type
     */
    $dir = getcwd();
    $filename = 'logs/oc_orders' . date('Y-m-d-H-mi') . '.csv';
    $handle = fopen( $filename, 'w' );
    if ( $handle ) {
        fwrite( $handle, "Account, Customer, Code, Name, Size, Unit," . 
                " Start Date, End Date, Frequency, Day of Week, Skip Days, Pickup Time," . 
                " Location, Zone, Quantity, \n" );
        foreach ($orders as $order) {
            $days_skipped = '';
            $multi = false;
            foreach ( $order->allDays() as $d ) {
                if ( $order->skipDay( $d ) ) {
                    if ( $multi ) {
                        $days_skipped .= ', ';
                    }
                    $days_skipped .= $d; 
                    $multi = true;
                }
            }
            $it = $order->getItem();
            if ( $it ) {
                $customer_name = $order->getAccount()->getDefaultCustomerId() == 0 ? $order->getCustomerName() : '---';
                $line = Utils::quoteString( $order->getAccountName()). ', ' 
                    . Utils::quoteString( $customer_name ) . ', ' .  $it->getCode() . ', ' 
                    . Utils::quoteString( $it->getName()) . ', '
                    . $it->getSize() . ', ' . $it->getUnit() . ', '
                    . Utils::formatDate( $order->getStartDate() ) . ', '
                    . Utils::formatDate( $order->getEndDate() ) . ', '
                    . $order->getFrequency() . ', '
                    . $order->getDayOfWeek() . ', '
                    . $days_skipped . ', '
                    . $order->getDeliveryTime()->format( 'H:i' ) . ', '
                    . $order->getLocationName() . ', '
                    . $order->getDeliveryZone() . ', '
                    . $order->getQuantity()  . "\n";
                fwrite( $handle, $line  );
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

