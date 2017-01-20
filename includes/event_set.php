<?php
    require_once("database.php");

    /*
     * Event Set
     * 
     * Models a row of the event_sets table in the database.
     * 
     */

    class EventSet extends DatabaseObject {

        protected static $table_name = "event_sets";
        protected static $fields = "*";
        protected static $sql_id = "id";
        protected static $sql_user_id = "user_id";
        protected static $form_name = "event_sets";
        public $id;
        public $user_id;
        public $name;
        public $favorite;

        /*
         * Check whether this event_set already exists in the database. Useful for
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
            $sql = replace_trailing_AND($sql);

            if ($row = $database->fetch_assoc($database->query($sql))) {
                return $row['id']; //use the first duplicate id found
            } else {
                return false;
            }
        }

        /**
         * Returns the IDs of the events of this event set
         * 
         * @global Object $database
         * @return Array
         */
        public function get_event_ids() {
            global $database;
            $event_ids = [];

            // Get event IDs in this event_set
            $sql = "SELECT event_id ";
            $sql .= "FROM event_sets ";
            $sql .= "JOIN event_sets_events ";
            $sql .= "ON event_sets.id = event_sets_events.event_set_id ";
            $sql .= "JOIN events ";
            $sql .= "ON event_sets_events.event_id = events.id ";
            $sql .= "WHERE event_set_id = " . $this->id;
            $event_ids_result = $database->query($sql);

            // build array of the event IDs 
            while ($event_id = $database->fetch_assoc($event_ids_result)) {
                $event_ids[] = $event_id['event_id'];
            }
            return $event_ids;
        }

        /**
         * Returns an array of event objects contained in this event set.
         * 
         * @return Array of Events
         */
        public function get_events() {

            // get the event IDs
            $ids = $this->get_event_ids();
            if (empty($ids)) {
                $events = false;
            } else {

                // build array of the event objects 
                $events = [];
                foreach ($ids AS $id) {
                    $events[] = Event::find_by_id($id);
                }
            }
            return $events;
        }

        /**
         * Inserts a row in event_set_event for the given event to be added to
         * this event set (where event_set_events rows represent a many-to-many 
         * relation between event sets and events).A helper function for the 
         * add_event() function.
         * 
         * @global Object $database
         * @global Object $session
         * @param Integer $id
         */
        protected function insert_event($id) {
            global $database;
            global $session;

            // Insert new events_contacts (for the many-to-many relationship 
            // between the shows table and contacts table)
            if ($database->query("INSERT INTO event_sets_events ( event_set_id, event_id ) VALUES ( " . $this->id . ", " . $id . ")")) {
                $session->add_message("Event #" . $id . " was added to event set " . $this->name . ".");
            } else {
                $session->add_error("Event #" . $id . " could not be added to event set " . $this->name . ".");
            }
        }

        /**
         * Deletes a row in event_set_event for the given event to be removed 
         * from this event set (where event_set_events rows represent a 
         * many-to-many relation between event sets and events).A helper 
         * function for the remove_event() function.
         * @global Object $database
         * @global Object $session
         * @param Integer $id
         */
        protected function delete_event($id) {
            global $database;
            global $session;

            // Insert new events_contacts (for the many-to-many relationship 
            // between the shows table and contacts table)
            if ($database->query("DELETE FROM event_sets_events WHERE event_set_id=" . $this->id . " AND event_id=" . $id)) {
                $session->add_message("Event #" . $id . " deleted sucessfully from " . $this->name . ".");
            } else {
                $session->add_error("Event #" . $id . " could not be deleted from " . $this->name . ".");
            }
        }

        /**
         * Checks whether a given event ID is alredy associated with an event in 
         * this event set.
         * 
         * @param Integer $new_id
         * @return boolean
         */
        protected function already_in_set($new_id) {
            $in_set = false;
            $ids = $this->get_event_ids();

            foreach ($ids AS $id) {
                if ($id == $new_id) {
                    $in_set = true;
                }
            }
            return $in_set;
        }

        /**
         * Adds Event(s) to this event set.
         * 
         * @global Object $session
         * @param Array of Inegers or an Integer
         */
        public function add_event($id) {
            global $session;

            // Array given
            if (is_array($id)) {
                $invalid_ids = "";
                $failure_occured = false;
                foreach ($id AS $event_id) {
                    if (Event::exists_by_id($event_id) && !$this->already_in_set($event_id)) {
                        $this->insert_event($event_id);
                    } else {
                        
                        // keep track of  any failures to inform user
                        $failure_occured = true;
                        $invalid_ids .= "#" . $event_id . ", ";
                    }
                }
                
                // Inform user which events could not be added to this event set.
                if ($failure_occured) {
                    $session->add_error("Events: " . remove_trailing_comma($invalid_ids) . " could not be added to " . $this->name . ". Either no such events exists or they are already members of this event set.");
                }

            // Int given
            } elseif (is_int($id)) {
                if (Event::exists_by_id($id) && !$this->already_in_set($id)) {
                    $this->insert_event($id);
                } else {
                    $session->add_error("Event #" . print_r($id) . " could not be added to " . $this->name . ". Either no such event exists or it is already a member of this event set.");
                }
            // $id is neither an Array or Integer
            } else {
                $session->add_error("Event could not be added to " . $this->name . ". Invalid event ID.");
            }
        }

        /**
         * Remove Event(s) from this event set.
         * 
         * @global Object $session
         * @param Array of Inegers or an Integer $id
         */
        public function remove_event($id) {
            global $session;
            
            // Array given
            if (is_array($id)) {
                $failure_occured = false;
                $invalid_ids = "";
                foreach ($id AS $event_id) {
                    if (Event::exists_by_id($event_id) && $this->already_in_set($event_id)) {
                        $this->delete_event($event_id);
                    } else {
                        // keep track of  any failures to inform user
                        $failure_occured = true;
                        $invalid_ids .= $event_id . ", ";
                    }
                }
                // Inform user which events could not be removed to this event set.
                if ($failure_occured) {
                $session->add_error("Events: " . remove_trailing_comma($invalid_ids) . " could not be deleted from " . $this->name . ". Either no such events exists or they are not members of this event set.");
                }
                
            // Integer given
            } elseif (is_integer($id)) {
                if (Event::exists_by_id($id) && $this->already_in_set($id)) {
                    $this->delete_event($id);
                } else {
                    $session->add_error("Event #" . $id . " could not be deleted from " . $this->name . ". Either no such event exists or it is not a member of this event set.");
                }
            
            // $id is neither an Array or Integer
            } else {
                $session->add_error("Event could not be deleted from " . $this->name . ". Invalid event ID.");
            }
        }

        /**
         * Inserts this event set into the database.
         * 
         * @global Object $database
         * @global Object $session
         * @return boolean
         */
        public function create() {
            global $database;
            global $session;

            if ($id = $this->is_in_the_database()) {
                $this->id = $id;
                $session->add_message("Event set creation skipped because it already exists.");
                return true;
            } else {
                // Add new contact
                $sql = "INSERT INTO " . self::$table_name . " ( ";
                $sql .= " user_id, name";
                $sql .= " ) VALUES ( ";
                $sql .= $database->escape_string_query($session->user_id) . ", '";
                $sql .= $database->escape_string_query($this->name) . "')";

                if ($database->query($sql)) {
                    $this->id = $database->insert_id(); // add ID to the model
                    $session->add_message("Event set was created sucessfully.");
                    return true;
                } else {
                    $session->add_error("Event set creation failed.");
                    return false;
                }
            }
        }

        /**
         * Updates a particular row of the event_set table of the database based 
         * on the calling Event object.
         * 
         * @global Object $database
         * @global Object $session
         * @return boolean
         */
        public function update() {
            global $database;
            global $session;
            $sql = "UPDATE " . self::$table_name . " SET ";
            $sql .= "name='" . $database->escape_string_query($this->name) . "', ";
            $sql .= "favorite=" . $database->escape_string_query($this->favorite) . " ";
            $sql .= "WHERE id=" . $database->escape_string_query($this->id);

            if ($database->query($sql)) {
                $session->add_message("Event set was modified sucessfully.");
                return true;
            } else {
                $session->add_error("Event set modification failed.");
                return false;
            }
        }

        /**
         * Deletes a particular row corresponding to the id of the calling 
         * object (assuming one exists).
         * 
         * @global Object $database
         * @global Object $session
         * @return boolean
         */
        public function delete() {
            global $database;
            global $session;

            $sql = "DELETE FROM " . self::$table_name . " WHERE id=";
            $sql .= $this->id;

            if ($database->query($sql)) {
                $session->add_message("Event set: " . $this->name . " was deleted sucessfully.");
                return true;
            } else {
                $session->add_error("Event set; " . $this->name . " could not be deleted.");
                return false;
            }
        }

        /**
         * Return those events in this event set which are favorites.
         * 
         * @global Object $session
         * @return EventSet
         */
        public static function find_favorites() {
            global $session;

            $sql = "SELECT * FROM " . self::$table_name . " ";
            $sql .= "WHERE " . self::$sql_user_id . " = " . $session->user_id . " ";
            $sql .= "AND favorite = TRUE";

            return self::find_by_sql($sql);
        }

        /**
         * Toggles this event set as favorite / non-favorite.
         * 
         * @global Object $session
         * @global Object $database
         * @return type
         */
        public function toggle_favorite() {
            global $session;
            global $database;

            $sql = "UPDATE " . self::$table_name . " ";
            $sql .= "SET favorite = " . !$this->favorite . " ";
            $sql .= "AND favorite = TRUE";

            if (!$database->query($sql)) {
                $session->add_error("Favorite toggle failed on " . $this->name . ".");
            }
        }

    }
    