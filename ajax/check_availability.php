<?php
    /*
     * Ajax username availavility check
     * 
     * Useful for user creation form validation.
     */
    if (!empty($_POST["username"])) {
        $query = "SELECT count(*) FROM users WHERE username='" . $_POST["username"] . "'";
        $match_result = $database->query($query);
        //confirm_query($match_result);
        $row = $database->fetch_row($match_result);
        $user_count = $row[0];
        if ($user_count > 0) {
            echo "<span class='status-not-available'> Username Not Available.</span>";
        } else {
            echo "<span class='status-available'> Username Available.</span>";
        }
        mysqli_free_result($match_result);
    }

