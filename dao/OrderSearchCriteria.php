<?php


/**
 * Search criteria for {@link OrderDao}.
 * <p>
 * Can be easily extended without changing the {@link OrderDao} API.
 */
final class OrderSearchCriteria {

    private $customer_id = null;
    private $account_id = null;
    private $show_expired = false;

    public function hasFilter() {
        if ( $this->customer_id || $this->account_id ) {
            return true;
        }
        return false;
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
    public function setShowExpired( $sense ) {
        $this->show_expired = $sense;
    }
 
    public function getShowExpired() {
        return $this->show_expired;
    }
}
