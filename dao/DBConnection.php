<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBConnection
 *
 * @author steve
 */
final class DBConnection {
    /** @var PDO */
    private static $db = null;

    protected function __construct() {}
    
    private function __clone() {}
    
    private function __wakeup() {}
    
    /**
     * @return PDO
     */
    public static function getDb() {
        if (self::$db !== null) {
            return self::$db;
        }
        $config = Config::getConfig("db");
        try {
            self::$db = new PDO($config['dsn'], $config['username'], $config['password'], 
                    array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return self::$db;
    }

    
}
