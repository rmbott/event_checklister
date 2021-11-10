<?php
    require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }
    

    /*
     * Updates an event along with a primary contact for that event.
     */
    
    if (isset($_GET['submit']) && sizeOf($_GET) < 2) {
        $session->add_error("At least one event must be selected.");
        redirect_to("dashboard.php"); 
    } else {
        echo "<h2>";
        print_r($_GET);
        echo "</h2>";
// Get Events from ids POSTed from corresponding DashboardElements
        $events = [];
        foreach ($_GET AS $id => $value) {
            if ($value == "on") {
                $events[] = Event::find_by_id($id);
            }
        }
        $events_contacts = [];
        foreach ($events AS $event) {
            $events_contacts[] = ["event" => $event, "contact" => $event->get_primary_contact()];
        }
    }

    if (isset($_POST['submit'])) {

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
            $event->id = $_POST['event_id'];
            $event->user_id = $session->user_id;
            $event->type = $_POST['type'];
            $event->date = $_POST['date'];
            $event->time = $_POST['time'];
            $event->url = $_POST['showpage_url'];

            // Update the Event in the database and inform the user

            if ($event->update($_POST['event_id'])) {
                $session->add_message("Event updated.");
            } else {
                $session->add_message("Event update failed.");
            }

            // Create Contact
            $contact = new Contact();
            $contact->id = $_POST['contact_id'];
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
            if ($contact->update()) {
                $session->add_message("Contact updated.");
            } else {
                $session->add_message("Contact update failed.");
            }
        }
        redirect_to("dashboard.php");

        // This is probably a GET request
    } // end: if (isset($_POST['submit']))
?>
<?php include("../../includes/layouts/header.php"); ?>
<div class="container">
    <h2>Modify Event</h2>
<?php echo $session->message(); ?>
<?php echo $session->error(); ?>
    <form action="update_event_contact.php" method="post">
        <input type="hidden" name="event_id" id="event_id" value="<?php 
            // Preserve the event and contact ids across the POST
            // Notice all but the first event selected from dashboard.php is ignored
            echo isset($_GET['submit']) ? $events_contacts[0]['event']->id : ""; 
            ?>">
        <input type="hidden" name="contact_id" id="event_id" value="<?php 
            // Preserve the event and contact ids across the POST
            // Notice all but the first event selected from dashboard.php is ignored
            echo isset($_GET['submit']) ? $events_contacts[0]['contact']->id : ""; 
            ?>">

        <h3>Event Info</h3>
        <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">

<?php
    // Create event type radio buttons
    $event_types = ["Cooking", "Catalog", "Fundraiser", "Wedding"];
    for ($i = 0; $i <= 3; $i++) {
        $ch = ($i == 0) ? "checked" : "";
        echo "<div class=\"radio-inline\">";
        echo "<label>";
        echo "<input type=\"radio\" name=\"type\" id=\"" . ($i + 1) . "\" value=\"" . ($i + 1) . "\" " . $ch . "/>" . $event_types[$i];
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
        echo "<input class=\"form-control\" type=\"" . $type . "\" name=\"" . $field . "\" id=\"" . $field . "\">";
        echo "</div>";
    }
?>
        </div>
        <h3>Contact Info</h3>
        <div class="contact-fields">
            <div class="form-group">
<?php
    $contact_fields = ["first_name", "last_name", "email",
        "day_phone", "evening_phone", "cell_phone", "addr1", "addr2",
        "addr3", "city", "state", "zip", "invite_list_url"];

    foreach ($contact_fields AS $field) {
        echo "<label class=\"col-sm-3 control-label\" for=\"" . $field . "\">" . fieldname_as_text($field) . "</label>";
        echo "<div class=\"col-sm-9\">";
        echo "<input class=\"form-control\" type=\"text\" name=\"" . $field . "\" id=\"" . $field . "\" value=\"\">";
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

                // get the existing event and primary contact from PHP
                var ec = JSON.stringify(<?php global $events_contacts;
                echo json_encode($events_contacts) ?>["0"]);
                var e = JSON.parse(ec).event;
                var c = JSON.parse(ec).contact;

                // set the event type radio fields
                var event_type = e.type;
                $('input[name=type]').prop("checked", "false");
                $("#" + event_type).prop("checked", "true");

                // Set the other event fields 
                $("#date").val(e.date);
                $("#time").val(e.time);
                $("#url").val(e.url);

                // Set the contact fields
                $("#first_name").val(c.first_name);
                $("#last_name").val(c.last_name);
                $("#email").val(c.email);
                $("#day_phone").val(c.day_phone);
                $("#evening_phone").val(c.evening_phone);
                $("#cell_phone").val(c.cell_phone);
                $("#addr1").val(c.addr1);
                $("#addr2").val(c.addr2);
                $("#addr3").val(c.addr3);
                $("#city").val(c.city);
                $("#state").val(c.state);
                $("#zip").val(c.zip);
                $("#invite_list_url").val(c.invite_list_url);

            });
        </script>      
</div>
</body>
</html>