<?php

    require_once("database.php");
    require_once("functions.php");
    
    /*
     * Contact Class
     * 
     * Models a row of the contacts table in the database.
     */

    class Contact extends DatabaseObject {

        protected static $table_name = "contacts";
        protected static $fields = "*";
        protected static $sql_id = "id";
        protected static $sql_user_id = "user_id";
        protected static $form_name = "contacts";
        public $id;
        public $user_id;
        public $first_name;
        public $last_name;
        public $email;
        public $day_phone;
        public $evening_phone;
        public $cell_phone;
        public $addr1;
        public $addr2;
        public $addr3;
        public $city;
        public $state;
        public $zip;
        public $invite_list_url;

        /*
         * Insert this contact into the database and update the id parameter. 
         * Unless an identical record already exists. In that case just up date 
         * the id parameter to match the first occurance of that record.
         * 
         * @returns false, if no duplicate found and the id of the first 
         * duplicate found otherwise.
         */

        public function create() {
            global $database;
            global $session;

            if ($id = $this->is_in_the_database()) {
                $this->id = $id;
                return true;
            } else {
                $params = get_object_vars($this);

                // Add new contact
                $sql = "INSERT INTO " . self::$table_name . " ( ";
                $sql .= " user_id, first_name, last_name, email, day_phone, 
                evening_phone, cell_phone, addr1, addr2, addr3, city, state, 
                zip, invite_list_url";
                $sql .= " ) VALUES ( ";
                $sql .= $session->user_id . ", ";
                foreach ($params AS $key => $value) {
                // skip id parameter
                if ($key == "id" || $key == "user_id") {
                    continue;
                } else {
                    $sql .= sql_quotes($database->escape_string_query($this->$key)) . ", ";
                }
            }
            $sql = remove_trailing_comma($sql) . ")";
                
                if ($database->query($sql)) {
                    $this->id = $database->insert_id();
                    return true;
                } else {
                    return false;
                }
            }
        }

        /*
         * Check whether this contact already exists in the database. Useful for
         * avoiding duplicate records.
         * 
         * @returns false, if no duplicate found and the id of the first 
         * duplicate found otherwise.
         */

        public function is_in_the_database() {
            global $database;
            global $session;
            $params = get_object_vars($this);

            // Construct the query by iterating over this contacts parameters
            $sql = "SELECT * FROM " . self::$table_name . " ";
            $sql .= "WHERE ( ";
            foreach ($params AS $key => $value) {
                // skip id parameter
                if ($key == "id") {
                    continue;
                } else
                // get user_id from session
                if ($key == "user_id") {
                    $sql .= $key . " = " . sql_quotes($database->escape_string_query($session->user_id)) . " AND ";
                }
                // everything else
                else {
                    $sql .= $key . " = " . sql_quotes($database->escape_string_query($value)) . " AND ";
                }
            }
            $sql = $this->replace_trailing_AND($sql);
            
            if ($row = $database->fetch_assoc($database->query($sql))) {
                return $row['id']; //use the first duplicate id found
            } else {
                return false;
            }
        }

        /*
         * A helper function for: is_in_the_database(). Replaces the last 
         * occuring "AND" with a ")" to fix the SQL syntax.
         * @parameter $string
         * @returns $string
         */

        private function replace_trailing_AND($string) {
            return str_lreplace($string, "AND", ")");
        }

        
        // Updates an existing row in the database. 
        public function update() {
            global $database;
            $params = get_object_vars($this);
            $sql = "UPDATE " . self::$table_name . " SET ";
            foreach ($params AS $key => $value) {
                // skip id parameter
                if ($key == "id" || $key == "user_id") {
                    continue;
                } else {
                    $sql .= $key . "=" . sql_quotes($database->escape_string_query($this->$key)) . ", ";
                }
            }
            $sql = remove_trailing_comma($sql);
            $sql .= "WHERE id=" . $database->escape_string_query($this->id);
            
            if ($database->query($sql)) {
                return true;
            } else {
                return false;
            }
        }
    }
    