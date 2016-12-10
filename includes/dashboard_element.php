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


    }
    