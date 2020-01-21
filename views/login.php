<!DOCTYPE html>
<html>
<body>
    <form class="form-horizontal" action="index.php?a=view&m=login" method="post">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="username">Username</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" name="username" id="username" value="<?php echo keep_form_field('username'); ?>">
            </div>
            <label class="col-sm-2 control-label" for="password">Password</label>
            <div class="col-sm-10">
                <input class="form-control" type="password" name="password" id="password" value="">
            </div>
            <div class="col-sm-12">
                <input class="btn btn-default pull-right" type="submit" name="submit" value="Submit" />
                <input class="btn btn-default pull-right" type="reset">
            </div>
        </div>
    </form>
	<script>
        // Reset form, since keep_form_field() broke the html reset-button.
        $(":reset").click(function () {
            $("#username").val("");
            $("#password").val("");
        });
    </script>

    <h1>or ...</h1>
    <div class="col-sm-10 col-sm-offset-2">
        <a href="create_user.php">Create a user</a>
    </div>
</div>
</body>
</html>