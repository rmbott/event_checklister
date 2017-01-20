<?php
    /*
     * Checks whether an event set of the same name already exists.
     * 
     * Useful for event set creation form validation.
     */
    if (!empty($_POST["name"])) {
        $query = "SELECT count(*) FROM event_sets WHERE user_id = ". $session->user_id. " AND name='" . $_POST["name"] . "'";
        $match_result = $database->query($query);
        $row = $database->fetch_row($match_result);
        $user_count = $row[0];
        if ($user_count > 0) {
            echo "<span class='status-not-available'> An event set with this name already exists.</span>";
        } else {
            echo "<span class='status-available'> Name available.</span>";
        }
        mysqli_free_result($match_result);
    }