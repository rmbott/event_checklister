<?php
    require_once("../../includes/initialize.php");
    
    /*
     * Activates user and verifies the email supplied at user creation. This 
     * page handles the URL generated in user activation emails. f the email and
     * hash match, then the associated user's "permissions" is increased from 
     * "5" (Inactive) to "4" (Active). 
     */
    if (isset($_GET['email']) && isset($_GET['hash'])) {

        $email = $database->escape_string_query($_GET["email"]);
        $hash = $database->escape_string_query($_GET['hash']);
        $sql = "SELECT id FROM users WHERE email='{$email}' AND verificationhash='{$hash}'";
        $match_result = $database->query($sql);
        $matches = $database->num_rows($match_result);
        $database->free_result($match_result);
        if ($matches > 0) {
            // Found a match - Activate user
            $sql = "UPDATE users SET permissions=4 WHERE email='{$email}' AND verificationhash='{$hash}'";
            if ($database->query($sql)) {
                $session->add_message("Activation complete. Login to continue.");
                redirect_to("login.php");
            } else {
            // Database problem - handle all problems generically below.
            }
            
        }
        // No matches found- handle all problems generically below.
    }
    // URL problem - handle all problems generically below.
    $session->add_error("Activation failed. Try the link again.");
    redirect_to("login.php");
?>