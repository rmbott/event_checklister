<?php
    require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }

    /*
     * Displays a table of events. Specifically, a table of DashboardElements 
     * which is a non-ehaustive but representative sample of event and primary 
     * contact information. Events can be selected. Buttons use dynamic 
     * submission which provides a way of acting upon selected events in various 
     * ways (e.g. C.R.U.D. on selected events). 
     * 
     */

    // Event Set
    if (!empty($_GET['event_set']) && EventSet::exists_by_id($_GET['event_set'])) {
        $event_set_id = $_GET['event_set'];

        // Toggle favorite event set
        if (!empty($_GET['toggle_es_fav'])) {
            $es = EventSet::find_by_id($event_set_id);
            $es->favorite = $es->favorite ? 0 : 1;
            $es->update();
        }
    }

    include("../../includes/layouts/header.php");
?>
<div class="container">
<ul class="nav nav-pills">
    <li><a href="dashboard.php">Events</a></li>
    <li><a href="create_event_contact.php">Create Event</a></li>
    <li><a href="import_contacts.php">Import Contacts</a></li>

    <!-- Event Sets -->
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Event Sets
            <span class="caret"></span></a>
        <ul class="dropdown-menu">

            <!-- Create Event Set -->
            <li><a href="#" onclick="document.dashboard.action = 'create_event_set_a.php'; document.dashboard.target = '_self'; document.dashboard.submit();">New (from selected events)</a></li>


            <!-- Current event set -->
            <?php if (isset($event_set_id)) { ?>

                    <!-- Delete event set -->
                    <li><a href="#" onclick="confirmDelete('event');">Delete (current)</a></li>
                    <form action="" name="delete_event_set" id="delete_event_set" method="post">
                        <input type="hidden" name="event_set_id" value="<?php echo $event_set_id; ?>">
                    </form>

                    <!-- Delete event set -->
                    <li><a href="#" onclick="document.update_event_set.action = 'update_event_set.php'; document.update_event_set.target = '_self'; document.update_event_set.submit();;">Rename (current)</a></li>
                    <form action="" name="update_event_set" id="update_event_set" method="post">
                        <input type="hidden" name="event_set_id" value="<?php echo $event_set_id; ?>">
                    </form>


                    <!-- Toggle favorite -->
                    <?php
                    if (EventSet::find_by_id($event_set_id)->favorite) {
                        ?>
                        <li><a href="?event_set=<?php echo $event_set_id ?>&toggle_es_fav=1">Unfavorite</a></li>
                    <?php } else {
                        ?>
                        <li><a href="?event_set=<?php echo $event_set_id ?>&toggle_es_fav=1">Favorite</a></li>
                        <?php
                    }
                }

                // List non-favorites    
                $event_sets = EventSet::find_all();
                foreach ($event_sets AS $event_set) {
                    if (!$event_set->favorite) {
                        echo "<li><a href=\"dashboard.php?event_set=" . $event_set->id . "\">" . $event_set->name . "</a></li>";
                    }
                }
            ?>
        </ul>
    </li>
    <li><a href="logout.php">Logout</a></li>
</ul>
<div class="container">
    <?php
        // Messages
        echo $session->message();

        // Errors
        echo $session->error();

        // Event set Favorites
        if ($es_favs = EventSet::find_favorites()) {
            echo "<div class=\"row event-set-favorites\">";
            foreach ($es_favs AS $favorite) {
                echo "<a href=\"dashboard.php?event_set=" . $favorite->id . "\">" . htmlentities($favorite->name) . "</a>";
                echo "&nbsp&nbsp";
            }
            echo "</div>";
        }

        // Contact set favorites
//            if ($cs_favs = ContactSet::find_favorites()) {
//                echo "<div class=\"row contact-set-favorites\">";
//                foreach ($cs_favs AS $favorite) {
//                    echo "<a href=\"dashboard.php?event_set=" .$favorite->id . "\">" .htmlentities($favorite->name). "</a>";
//                }
//                echo "</div>";
//            }
    ?>
    <div class="row dashboard-row">
        <section class="col-xs-12">
            <h1><?php echo isset($event_set_id) ? EventSet::find_by_id($event_set_id)->name : "Events"; ?></h1>


            <form action="" name="dashboard" id="dashboard" method="post">
                <div class="form-group">
                    <?php
                        if (isset($event_set_id)) {
                            $table = new Table(DashboardElement::find_by_event_set_id($event_set_id));
                        } else {
                            $table = new Table(DashboardElement::find_all());
                        }
                        echo $table->display();
                    ?>
                </div>
                <div class="form-group">
                    <input class="btn btn-default pull-right" type="submit" name="submit" value="Event Checklist PDF(s)" form="dashboard" onclick="document.dashboard.action = 'event_checklist_pdf.php'; document.dashboard.target = '_self'; document.dashboard.submit(); return true;">
                    <input class="btn btn-default pull-right" type="submit" name="submit" value="Modify Event(s)" form="dashboard" onclick="document.dashboard.action = 'update_event_contact.php'; document.dashboard.method = 'get'; document.dashboard.target = '_self'; document.dashboard.submit(); return true;">
                    <!-- Consider adding a confirmation warning for the delete button -->
                    <input class="btn btn-default pull-right" type="submit" name="submit" value="Delete Event(s)" form="dashboard" onclick="document.dashboard.action = 'delete_event_contact.php'; document.dashboard.method = 'get'; document.dashboard.target = '_self'; document.dashboard.submit(); return true;">
                    <input class="btn btn-default pull-right" type="submit" name="submit" value="Host Packet PDF(s)" form="dashboard" id="host_packet">
                    </section>
                </div>
            </form>
    </div>
    <script>
        $(document).ready(function () {

            // Hide buttons if there are no event to work with
            if ($('#no_events').length) {
                $(".btn").css({display: "none"});
            }

            // Resize the table based on contents
            function resizeInput() {
                $(this).attr('size', $(this).val().length + 1);
            }
            $('input[type="text"]')
                    // event handler
                    .keyup(resizeInput)
                    // resize on page load
                    .each(resizeInput);
        });

        // Confirmation dialog for deleting sets
        function confirmDelete(setType) {
            if (confirm("Delete the current " + setType + " set? (Note: This only deletes the containing set, not the " + setType + "(s) within it.)")) {
                document.delete_event_set.action = 'delete_event_set.php';
                document.delete_event_set.target = '_self';
                document.delete_event_set.submit()
            }
        }

    </script>
</div>
</body>
</html>