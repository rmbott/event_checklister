<?php

    require_once(LIB_PATH.DS."db_config.php");

    /*
     * MySqlDatabase Class
     * 
     * Models the database. While a MySQL database is used exclusively, many of 
     * the methods here simply abstract away the specifics involved with 
     * interacting with a MySQL database. In this way support for other database 
     * technologies can be added by adding additional classes like this one.
     */
    
    class MySQLDatabase {

        private $connection;

        function __construct() {
        $this->open_connection();
        }

        public function open_connection() {

            // Connect
            $this->connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

            // Test whether connection suceeded
            if (mysqli_connect_errno()) {
                die("Database connection failed: " .
                        mysqli_connect_error() .
                        " (" . mysqli_connect_errno() . ")"
                );
            }
        }
        
        public function query($sql) {
            $result = mysqli_query($this->connection, $sql);
            $this->confirm_query($result);
            return $result;
        }
        
        private function confirm_query($result) {
            if (!$result) {
                die("Mysql database query failed.");
            }
        }
        
        public function escape_string_query($string) {
            return mysqli_real_escape_string($this->connection, $string);
        }
        
        public function num_rows($result) {
            return mysqli_num_rows($result);
        }
        
        public function insert_id() {
            return mysqli_insert_id($this->connection);
        }
        
        public function affected_rows($result) {
            return mysqli_affected_rows($this->connection);
        }

        public function close_connection() {
            if (isset($this->connection)) {
                mysqli_close($this->connection);
                unset($this->connection);
            }
        }
        
        public function fetch_array($result) {
            return mysqli_fetch_array($result);
        }
        
        public function fetch_assoc($result) {
            return mysqli_fetch_assoc($result);
        }
        
        public function fetch_all($result) {
            return mysqli_fetch_all($result);
        }
        
        public function fetch_row($result) {
            return mysqli_fetch_row($result);
        }
        
        public function error() {
            return mysqli_error($this->connection);
        }
        
        public function free_result($result) {
            return mysqli_free_result($result);
        }

    }

    $database = new MySQLDatabase();
?>