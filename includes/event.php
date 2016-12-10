<?php

    require_once("database.php");
    
    /*
     * Event Class
     * 
     * Models a row of the event table in the database.
     * 
     */

    class Event extends DatabaseObject {

        protected static $table_name = "events";
        protected static $fields = "*";
        protected static $sql_id = "id";
        protected static $sql_user_id = "user_id";
        protected static $form_name = "events";
        public $id;
        public $user_id;
        public $type;
        public $date;
        public $time;
        public $url;

        // Returns a contact object of the primary contect associated with the 
        // calling event.
        public function get_primary_contact() {
            global $database;
            
            // Get contact id
            $contact_id_result = $database->query("SELECT contact_id FROM " . JOIN_EVENTS_CONTACTS . " WHERE event_id = " . $this->id);
            $contact_id = $database->fetch_assoc($contact_id_result)["contact_id"];
            
            // Get Contact
            return Contact::find_by_id($contact_id);
        }
        
        // Inserts a new row in the event table of the database based on the 
        // calling Event object.
        public function create() {
            global $database;
            $sql = "INSERT INTO " . self::$table_name . " ( ";
            $sql .= " user_id, type, date, time, showpage_url";
            $sql .= " ) VALUES ( ";
            $sql .= $database->escape_string_query($this->user_id) . ", ";
            $sql .= $database->escape_string_query($this->type) . ", '";
            $sql .= $database->escape_string_query($this->date) . "', '";
            $sql .= $database->escape_string_query($this->time) . "', '";
            $sql .= $database->escape_string_query($this->url) . "')";
            if ($database->query($sql)) {
                $this->id = $database->insert_id();
                return true;
            } else {
                return false;
            }
            
        }
        
        // Updates a particular row of the event table of the database based on the 
        // calling Event object.
        public function update() {
            global $database;
            $sql = "UPDATE " . self::$table_name . " SET ";
            $sql .= "type=" . $database->escape_string_query($this->type) .", ";
            $sql .= "date='" . $database->escape_string_query($this->date). "', ";
            $sql .= "time='" . $database->escape_string_query($this->time) . "', ";
            $sql .= "showpage_url='" . $database->escape_string_query($this->url). "' ";
            $sql .= "WHERE id=" . $database->escape_string_query($this->id);

            if ($database->query($sql)) {
                return true;
            } else {
                return false;
            }
            
        }
        
        // Deletes a particular row corresponding to the id of the calling 
        // object (assuming one exists).
        public function delete() {
            global $database;
            
            $sql = "DELETE FROM " . self::$table_name . " WHERE id=";
            $sql .= $this->id;
            
            if ($database->query($sql)) {
                return true;
            } else {
                return false;
            }
        }
    }