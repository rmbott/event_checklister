<?php

    require_once("database.php");
    define("DASHBOARD_COLUMNS", "events.id, date, DATE_FORMAT(time, '%l:%i %p'), type, first_name, last_name, city, state, showpage_url");
    define("JOIN_EVENTS_CONTACTS", "events JOIN events_contacts ON events.id = events_contacts.event_id JOIN contacts ON events_contacts.contact_id = contacts.id");

    /*
     * DashboardElement Class
     * 
     * Models a row of the dashboard. That is a representative but incomplete 
     * summary of an event with it's associated primary contact. 
     */

    class DashboardElement extends DatabaseObject {

        protected static $table_name = JOIN_EVENTS_CONTACTS;
        protected static $fields = DASHBOARD_COLUMNS;
        protected static $sql_id = "events.id";
        protected static $sql_user_id = "contacts.user_id";
        protected static $form_name = "dashboard";
        public $id;
        public $date;
        public $time;
        public $type;
        public $first_name;
        public $last_name;
        public $city;
        public $state;
        public $zip;
        public $showpage_url;

        /**
         * Returns dashbord elements for the events contained in a given event 
         * set. Useful for constructing a table and displaying the contents of
         * an event set.
         * 
         * @global Object $session
         * @param Int $id
         * @return Array of DashboardElement
         */
        public static function find_by_event_set_id($id) {
            global $session;

            $event_set = EventSet::find_by_id($id);
            if ($ids = $event_set->get_event_ids()) {
                $sql = "SELECT * FROM " . self::$table_name;
                $sql .= " WHERE " . self::$sql_user_id . " = " . $session->user_id;
                $sql .= " AND (";
                foreach ($ids AS $event_id) {
                    $sql .= " events.id = " . $event_id . " OR";
                }
                $sql = replace_trailing_OR($sql);
                return DashboardElement::find_by_sql($sql);
            }
            return null;
        }

    }
    