<?php

    require_once("initialize.php");

    
    /*
     * DatabaseObject Class
     * 
     * Consolidates methods common to objects that extend this class, that is, 
     * those that model database rows. Namely, Event, Contact, User, and 
     * DashboardElement.
     * 
     */
    class DatabaseObject {

        protected static $table_name;

        // Database methods common to classes that model database rows
        public static function find_all() {
            global $session;
            return static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE " . static::$sql_user_id . " = ". $session->user_id);
        }

        public static function find_by_id($id = 0) {
            global $session;
            $result_array = static::find_by_sql("SELECT " . static::$fields . " FROM " . static::$table_name . " WHERE ( " . static::$sql_user_id . " = ". $session->user_id." AND " . static::$sql_id . " = {$id} ) LIMIT 1");
            return !empty($result_array) ? array_shift($result_array) : FALSE;
        }

        public static function find_by_sql($sql = "") {
            global $database;
            $object_array = [];
            $result_set = $database->query($sql);
            while ($row = $database->fetch_array($result_set)) {
                $object_array[] = static::instantiate($row);
            }
            return $object_array;
        }
        
        public static function exists_by_id($id = 0) {
            $result_array = static::find_by_id($id);
            return !empty($result_array);
        }

        private function has_attribute($attribute) {
            $object_vars = get_object_vars($this);
            return array_key_exists($attribute, $object_vars);
        }

        public static function instantiate($record) {
            // Could check that $record exists and is an array
            $class_name = get_called_class();
            $object = new $class_name;

            foreach ($record AS $key => $value) {
                // Use the unique event_id as the DashboardElement id
                // (as opposed to the non-unique contact_id)
                if ($class_name == "DashboardElement" && $key == "event_id") {
                    $object->id = $value;
                }
                if ($object->has_attribute($key)) {
                    $object->$key = $value;
                }
            }
            return $object;
        }

    }
    