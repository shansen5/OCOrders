<?php


/**
 * Search criteria for {@link OrderDao}.
 * <p>
 * Can be easily extended without changing the {@link OrderDao} API.
 */
final class OrderSearchCriteria {

    private $customer_id = null;
    private $account_id = null;

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
}
