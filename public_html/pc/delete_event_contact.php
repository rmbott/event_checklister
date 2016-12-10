<?php require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }
    
    /*
     * Deletes selected events. Part of event C.R.U.D. that is done via the 
     * dashboard.
     * 
     */

    if (isset($_GET['submit'])) {
        
        // Get Events from ids POSTed from corresponding DashboardElements
        $events = [];
        foreach ($_GET AS $id => $value) {
            if ($value == "on") {
                $events[] = Event::find_by_id($id);
            }
        }

        $events_contacts = [];
        $deleted = 0;
        $not_deleted = 0;
        
        // Delete selected events
        foreach ($events AS $event) {
            $new_event = new Event();
            $new_event->id = $event->id;
            if ($new_event->delete()) {
                $deleted++;
            } else {
                $not_deleted++;
            }
        } 
        $session->add_message($deleted. " events deleted.");
        $session->add_message($not_deleted . " deletions failed.");
        redirect_to("dashboard.php");
    }
   ?>