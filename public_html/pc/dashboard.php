<?php require_once("../../includes/initialize.php");
if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }
include("../../includes/layouts/header.php"); 

    /*
     * Displays a table of events. Specifically, a table of DashboardElements 
     * which is a non-ehaustive but representative sample of event and primary 
     * contact information. Events can be selected. Buttons use dynamic 
     * submission which provides a way of acting upon selected events in various 
     * ways (e.g. C.R.U.D. on selected events). 
     * 
     */

?>
<div class="container">
    <div class="row dashboard-row">
        <section class="col-xs-12">
            <h1>Events Dashboard</h1>
            <?php echo $session->message(); ?>
            <?php echo $session->error(); ?>

            <form action="" name="dashboard" id="dashboard" method="post">
                <div class="form-group">
                    <?php
                        $table1 = new Table(DashboardElement::find_all());
                        echo $table1->display();
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
                $(".btn").css({ display: "none" });
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

    </script>
</div>
</body>
</html>


