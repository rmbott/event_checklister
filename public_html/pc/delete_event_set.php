<?php
    require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }
    
    /*
     * Deletes an event set.
     */

    if (!empty($_POST['event_set_id'])) {

        $es = EventSet::find_by_id($_POST['event_set_id']);
        $es->delete();
        redirect_to("dashboard.php");

    }