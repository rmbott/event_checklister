<?php
    /*
     * Ajax for JQuery Autocomplete in create_event.php
     * 
     * Returns JSON encoded contacts that are "LIKE" the supplied search 
     * term, for a particular user. 
     */
    
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }
    
    //Get the search term (partial first or lastname)
    $searchTerm = $_GET['term'];
    $sql = "SELECT id, first_name, last_name, email, day_phone, evening_phone, cell_phone, addr1, addr2, addr3, city, state, zip, invite_list_url FROM contacts WHERE user_id = {$session->user_id} AND ( first_name LIKE '" . $searchTerm . "%' OR last_name LIKE '" . $searchTerm . "%' ) ORDER BY last_name ASC";
    
    //Get any matching contacts
    $result = $database->query($sql);
    $matches = [];
    
    // Organize the results
    while ($row = $database->fetch_assoc($result)) {
        $row['value'] = $row['first_name'];
        $row['label'] = "{$row['last_name']}, {$row['first_name']} - {$row['city']}, {$row['state']}";
        array_push($matches, $row);
    }
    $database->free_result($result);
    
    // Encode the results for javascript
    echo json_encode($matches);
?>
