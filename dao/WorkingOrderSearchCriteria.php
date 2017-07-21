<?php


/**
 * Search criteria for {@link WorkingOrderDao}.
 * <p>
 * Can be easily extended without changing the {@link WorkingOrderDao} API.
 */
final class WorkingOrderSearchCriteria {

    private $start_date = null;
    private $end_date = null;
    private $pickup_location_id = null;
    private $item_id = null;
    private $customer_id = null;
 
    public function hasFilter() {
        if ( $this->start_date || $this->end_date || $this->pickup_location_id
                || $this->item_id || $this->customer_id ) {
            return true;
        }
        return false;
    }
    
    public function setStartDate( $start ) {
        $this->start_date = $start;
    }
    
    public function getStartDate() {
        return $this->start_date;
    }


    public function setEndDate( $end ) {
        $this->end_date = $end;
    }
    
    public function getEndDate() {
        return $this->end_date;
    }

    public function setLocationId( $id ) {
        $this->pickup_location_id = $id;
    }
 
    public function getLocationId() {
        return $this->pickup_location_id;
    }

    public function setItemId( $id ) {
        $this->item_id = $id;
    }
 
    public function getItemId() {
        return $this->item_id;
    }

    public function setAccountId( $id ) {
        $this->account_id = $id;
    }
 
    public function getAccountId() {
        return $this->account_id;
    }
    public function setCustomerId( $id ) {
        $this->customer_id = $id;
    }
 
    public function getCustomerId() {
        return $this->customer_id;
    }
}
