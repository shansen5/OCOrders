<?php

/**
 * Miscellaneous utility methods.
 */
final class Utils {

    const ADMIN = 1;
    const USER = 2;
    
    private function __construct() {
    }

    /**
     * Generate link.
     * @param string $page target page
     * @param array $params page parameters
     */
    public static function createLink($page, array $params = array()) {
        $params = array_merge(array('page' => $page), $params);
        // TODO add support for Apache's module rewrite
        return 'index.php?' .http_build_query($params);
    }

    /**
     * Format date.
     * @param DateTime $date date to be formatted
     * @return string formatted date
     */
    public static function formatDate(DateTime $date = null) {
        if ($date === null) {
            return '';
        }
        return $date->format('Y-m-d');
    }

    /**
     * Format date and time.
     * @param DateTime $date date to be formatted
     * @return string formatted date and time
     */
    public static function formatDateTime(DateTime $date = null) {
        if ($date === null) {
            return '';
        }
        return $date->format('Y-m-d H:i');
    }

    /**
     * Redirect to the given page.
     * @param type $page target page
     * @param array $params page parameters
     */
    public static function redirect($page, array $params = array()) {
        header('Location: ' . self::createLink($page, $params));
        die();
    }

    /**
     * Get value of the URL param.
     * @return string parameter value
     * @throws NotFoundException if the param is not found in the URL
     */
    public static function getUrlParam($name) {
        if (!array_key_exists($name, $_GET)) {
            throw new NotFoundException('URL parameter "' . $name . '" not found.');
        }
        return $_GET[$name];
    }

    /**
     * Get {@link Item} by the identifier 'id' found in the URL.
     * @return Item {@link Item} instance
     * @throws NotFoundException if the param or {@link Item} instance is not found
     */
    public static function getItemByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No Item identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Item identifier provided.');
        }
        $dao = new ItemDao();
        $item = $dao->findById($id);
        if ($item === null) {
            throw new NotFoundException('Unknown Item identifier provided.');
        }
        return $item;
    }

    /**
     * Get {@link Location} by the identifier 'id' found in the URL.
     * @return Item {@link Location} instance
     * @throws NotFoundException if the param or {@link Location} instance is not found
     */
    public static function getLocationByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No Location identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Location identifier provided.');
        }
        $dao = new LocationDao();
        $location = $dao->findById($id);
        if ($location === null) {
            throw new NotFoundException('Unknown Location identifier provided.');
        }
        return $location;
    }

    /**
     * Get {@link Customer} by the identifier 'id' found in the URL.
     * @return Item {@link Customer} instance
     * @throws NotFoundException if the param or {@link Customer} instance is not found
     */
    public static function getCustomerByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No Customer identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Customer identifier provided.');
        }
        $dao = new CustomerDao();
        $customer = $dao->findById($id);
        if ($customer === null) {
            throw new NotFoundException('Unknown Customer identifier provided.');
        }
        return $customer;
    }

    public static function getUserRole() {
        if ( $_SESSION['oc_user_role'] == 'ADMIN' ) {
            return self::ADMIN;
        }
        return self::USER;
    }

    public static function getUserName() {
        return $_SESSION['oc_user'];        
    }
    
    public static function getUserIdByName() {
        $dao = new UserDao();
        $user = $dao->findByUsername($_SESSION['oc_user']);
        if ($user) {
            return $user->getId();
        }
        throw new NotFoundException('User not found: ' . $_SESSION['oc_user']);
    }
    
        /**
     * Get {@link User} by the identifier 'id' found in the URL.
     * @return Item {@link User} instance
     * @throws NotFoundException if the param or {@link User} instance is not found
     */
    public static function getUserByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No User identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid User identifier provided.');
        }
        return getUserById( $id );
    }

    public static function getUserById( $id ) {
        $dao = new UserDao();
        $user = $dao->findById($id);
        if ($user === null) {
            throw new NotFoundException('Unknown User identifier provided.');
        }
        return $user;
    }

    /**
     * Get {@link Order} by the identifier 'id' found in the URL.
     * @return Item {@link Order} instance
     * @throws NotFoundException if the param or {@link Order} instance is not found
     */
    public static function getOrderByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No Order identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Order identifier provided.');
        }
        $dao = new OrderDao();
        $order = $dao->findById($id);
        if ($order === null) {
            throw new NotFoundException('Unknown Order identifier provided.');
        }
        return $order;
    }

    /**
     * Get {@link WorkingOrder} by the identifier 'id' found in the URL.
     * @return WorkingOrder {@link WorkingOrder} instance
     * @throws NotFoundException if the param or {@link WorkingOrder} instance is not found
     */
    public static function getWorkingOrderByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No WorkingOrder identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid WorkingOrder identifier provided.');
        }
        $dao = new WorkingOrderDao();
        $order = $dao->findById($id);
        if ($order === null) {
            throw new NotFoundException('Unknown WorkingOrder identifier provided.');
        }
        return $order;
    }

    /**
     * @return array of id, first_name, last_name for all customers
     */
    public static function getAllCustomerIdAndName() {
        $dao = new CustomerDao();
        return $dao->getAllCustomerIdAndName();
    }
    
    /**
     * @return array of id, type, sub_type for all items
     */
    public static function getAllItems() {
        $dao = new ItemDao();
        $result = $dao->find();
        return $result;
    }
    
    /**
     * @return array of id, name for all locations
     */
    public static function getAllLocationIdAndName() {
        $dao = new LocationDao();
        return $dao->getAllLocationIdAndName();
    }
    
    /**
     * Capitalize the first letter of the given string
     * @param string $string string to be capitalized
     * @return string capitalized string
     */
    public static function capitalize($string) {
        return ucfirst(mb_strtolower($string));
    }

    /**
     * Escape the given string
     * @param string $string string to be escaped
     * @return string escaped string
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }

}
