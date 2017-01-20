<?php
    require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }

    /*
     * Intermediate step in event set creation. Passes event IDs posted to here
     * and tacks on a user chosen name.
     */

    if (!empty($_POST)) {

        print_r($_POST);
        // Put event ids POSTed from corresponding DashboardElements in array
        $events = [];
        foreach ($_POST AS $id => $value) {
            if ($value == "on" && is_int($id)) {
                $event_ids[] = $id;
            }
        }
        // save the IDs
        $_SESSION['event_set_ids'] = $event_ids;
    } else {
        // Empty array of IDs for an empty set
        $_SESSION['event_set_ids'] = [];
    }
    include("../../includes/layouts/header.php");
?>
<div class="container">
    <h1>Create user</h1>
    <?php echo $session->message(); ?>
    <?php echo $session->error(); ?>

    <form class="form-horizontal" action="create_event_set_b.php" method="post">
        <div class="form-group">
            <div class="" id="name_sec">
                <label class="col-sm-2 control-label" for="name">Name</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="name" id="name" value="<?php echo keep_form_field('name'); ?>">
                </div>
            </div>

            <!-- Display whether name is available or unavailable here. -->
            <span id="name-availability-status"></span><p><img src="LoaderIcon.gif" id="loaderIcon" style="display:none" /></p>

            <div class="col-sm-12">
                <input class="btn btn-default pull-right" type="submit" name="submit" value="Submit">
                <input class="btn btn-default pull-right" type="reset">
            </div>
        </div>
    </form>
    <script>

        // Reset form, since keep_form_field() broke the html reset-button.
        $(":reset").click(function () {
            $("#name").val("");
        });

        // See if an event set is available
        $("#name").blur(function () {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_ajax_check_event_set_availability.php",
                data: 'name=' + $("#name").val(),
                type: "POST",
                success: function (data) {
                    $("#name-availability-status").html(data);
                    $("#loaderIcon").hide();
                    if ($('span.status-not-available').length) {
                        $("#name_sec").attr("class", "has-error");
                    } else if ($('span.status-available').length) {
                        $("#name_sec").attr("class", "has-success");
                    }
                },
                error: function () {}
            });
        });

    </script>
</div>
</body>
</html>