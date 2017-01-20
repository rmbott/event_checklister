<?php

    require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }

    /*
     * Second and final step in event set creation. Inserts an event set into 
     * database. Inserts a row in event_set_event for any events to be added to
     * this event set (where event_set_events rows represent a many-to-many 
     * relation between event sets and events).
     */

    if (isset($_POST['name'])) {

        // Construct a model of the event set
        $es = new EventSet();
        $es->name = $_POST['name'];
        $es->user_id = $session->user_id;
        $es->favorite = FALSE;

        // Create the event set
        if ($es->create()) {
            if (!empty($_SESSION['event_set_ids'])) {
                $es->add_event($_SESSION['event_set_ids']);
            }
            
            // clean up event IDs from session
            unset($_SESSION['event_set_ids']);
            
            redirect_to("dashboard.php?event_set=" . $es->id);
        }
    } else {
        $session->add_error("Event set creation failed.");
        redirect_to("dashboard.php");
    }
