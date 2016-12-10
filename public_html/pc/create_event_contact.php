<?php require_once("../../includes/initialize.php"); ?>
<?php
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }
    $type = 1; // Default event type
?>
<?php
    
    /*
     * Creates an event along with a primary contact for that event.
     */
    if (isset($_POST['submit'])) {
        $type = $_POST['type'];

        // Validations
        $event_fields = ["user_id", "type", "date", "time", "showpage_url"];
        $required_fields = array("type", "date", "time", "first_name",
            "last_name", "email", "addr1", "city", "state", "zip");
        $fields_with_max_lengths = array("showpage_url" => 255,
            "first_name" => 30, "last_name" => 30, "email" => 50,
            "day_phone" => 20, "evening_phone" => 20, "cell_phone" => 20,
            "addr1" => 30, "addr2" => 30, "addr3" => 30, "city" => 30,
            "state" => 30, "zip" => 13, "invite_list_url" => 255);
        $disjunctive_fields = ["day_phone", "evening_phone",
            "cell_phone"];

        validate_presences($required_fields);
        validate_disjunctive_presences($disjunctive_fields);
        validate_max_lengths($fields_with_max_lengths);

        
        
        if ($session->error_check()) {

            // Create Event
            $event = new Event();
            $event->user_id = $session->user_id;
            $event->type = $_POST['type'];
            $event->date = $_POST['date'];
            $event->time = $_POST['time'];
            $event->url = $_POST['showpage_url'];

            // Insert the Event into the database and inform the user
            if ($event->create()) {
                $session->add_message("Event created.");
            } else {
                $session->add_message("Event creation failed.");
            }

            // Create Contact
            $contact = new Contact();
            $contact->user_id = $session->user_id;
            $contact->first_name = $_POST['first_name'];
            $contact->last_name = $_POST['last_name'];
            $contact->email = $_POST['email'];
            $contact->day_phone = $_POST['day_phone'];
            $contact->evening_phone = $_POST['evening_phone'];
            $contact->cell_phone = $_POST['cell_phone'];
            $contact->addr1 = $_POST['addr1'];
            $contact->addr2 = $_POST['addr2'];
            $contact->addr3 = $_POST['addr3'];
            $contact->city = $_POST['city'];
            $contact->state = $_POST['state'];
            $contact->zip = $_POST['zip'];
            $contact->invite_list_url = $_POST['invite_list_url'];

            // Insert the Contact into the database and inform the user
            if ($contact->create()) {
                $session->add_message("Contact created.");
            } else {
                $session->add_message("Contact creation failed.");
            }

            // Insert new events_contacts (for the many-to-many relationship 
            // between the shows table and contacts table)
            if ($database->query("INSERT INTO events_contacts ( event_id, contact_id ) VALUES ( " . $event->id . ", " . $contact->id . ")")) {
                $session->add_message("Event-Contact database relation created.");
            } else {
                $session->add_message("Event-Contact database relation creation failed.");
            }
        }

        // This is probably a GET request
    } // end: if (isset($_POST['submit']))
?>
<?php include("../../includes/layouts/header.php"); ?>
<div class="container">
    <h2>Create Event</h2>
    <?php echo $session->message(); ?>
    <?php echo $session->error(); ?>
    <form action="create_event_contact.php" method="post">
        <h3>Event Info</h3>
        <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">

                <?php
                    // Create event type radio buttons
                    $event_types = ["Cooking", "Catalog", "Fundraiser", "Wedding"];
                    for ($i = 1; $i <= 4; $i++) {
                        $ch = ($i == $type) ? "checked" : "";
                        echo "<div class=\"radio-inline\">";
                        echo "<label>";
                        echo "<input type=\"radio\" name=\"type\" value=\"" . ($i) . "\" " . $ch . "/>" . $event_types[$i - 1];
                        echo "</label></div>";
                    }
                ?>
            </div>
        </div>
        <div class="form-group">
            <?php
                // Create remaining event fields
                $event_fields = ["date", "time", "showpage_url"];

                foreach ($event_fields AS $field) {
                    $type = ($field == "showpage_url") ? "text" : $field;
                    echo "<label class=\"col-sm-3 control-label\" for=\"" . $field . "\">" . fieldname_as_text($field) . ":</label>";
                    echo "<div class=\"col-sm-9\">";
                    echo "<input class=\"form-control\" type=\"" . $type . "\" name=\"" . $field . "\" value=\"". keep_form_field($field)."\">";
                    echo "</div>";
                }
            ?>
        </div>
        <h3>Primary Contact Info</h3>
        <div class="contact-fields">
            <div class="form-group">
                <?php
                    $contact_fields = ["first_name", "last_name", "email",
                        "day_phone", "evening_phone", "cell_phone", "addr1", "addr2",
                        "addr3", "city", "state", "zip", "invite_list_url"];

                    foreach ($contact_fields AS $field) {
                        echo "<label class=\"col-sm-3 control-label\" for=\"" . $field . "\">" . fieldname_as_text($field) . "</label>";
                        echo "<div class=\"col-sm-9\">";
                        echo "<input class=\"form-control\" type=\"text\" name=\"" . $field . "\" id=\"" . $field . "\" value=\"". keep_form_field($field)."\">";
                        echo "</div>";
                    }
                ?>

            </div></div>
        <div class="form-group">
            <div class="col-sm-12">
                <input class="btn btn-default pull-right" type="submit" name="submit" value="Continue" />
                <input class="btn btn-default pull-right" type="reset">
            </div></div>

        <script>
            $(document).ready(function () {
                // Add instructions for searching existing contacts
                $("#first_name").prop("placeholder", "Start typing first or last name to search for existing contact");
                
                // Autocomplete existing contacts on the first_name field
                var ac_config = {
                    source: "get_ajax_autocomplete_contacts.php",
                    select: function (event, ui) {
                        $("#first_name").val(ui.item.first_name);
                        $("#last_name").val(ui.item.last_name);
                        $("#email").val(ui.item.email);
                        $("#day_phone").val(ui.item.day_phone);
                        $("#evening_phone").val(ui.item.evening_phone);
                        $("#cell_phone").val(ui.item.cell_phone);
                        $("#addr1").val(ui.item.addr1);
                        $("#addr2").val(ui.item.addr2);
                        $("#addr3").val(ui.item.addr3);
                        $("#city").val(ui.item.city);
                        $("#state").val(ui.item.state);
                        $("#zip").val(ui.item.zip);
                        $("#invite_list_url").val(ui.item.invite_list_url);
                    },
                    minLength: 1
                };
                $("#first_name").autocomplete(ac_config);
            });
        </script>      
</div>
</body>
</html>