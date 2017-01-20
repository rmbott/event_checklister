<?php
    require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }

    /*
     * Updates (renames) an event set.
     */

    if (!empty($_POST['event_set_id'])) {

        $old_name = EventSet::find_by_id($_POST['event_set_id'])->name;
    }

    // Update the event set
    if (!empty($_POST['name']) && !empty($_POST['es_id'])) {
        $es = EventSet::find_by_id($_POST['es_id']);
        $es->name = $_POST['name'];
        $es->update();
        redirect_to("dashboard.php");
    }
    include("../../includes/layouts/header.php");
?>
<div class="container">
    <h1>Create user</h1>
    <?php echo $session->message(); ?>
    <?php echo $session->error(); ?>

    <form class="form-horizontal" action="update_event_set.php" method="post">
        <div class="form-group">
            <div class="" id="name_sec">
                <label class="col-sm-2 control-label" for="name">Name</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="name" id="name" value="<?php echo isset($old_name) ? $old_name : ""; ?>">
                    <input class="hidden" name="es_id" value="<?php echo isset($_POST['event_set_id']) ? $_POST['event_set_id'] : ""; ?>">
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

        // See if a username is available
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