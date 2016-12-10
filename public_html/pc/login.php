<?php
    require_once("../../includes/initialize.php");
    if ($session->is_logged_in()) {
        redirect_to("index.php");
    }
    
    /*
     * Users can enter credentials for authentication. Non-users can head to 
     * user creation. 
     */

    if (isset($_POST['submit'])) {

        // Validation
        $required_fields = ['username', 'password'];
        validate_presences($required_fields);

        if ($session->error_check()) {

            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $found_user = User::authenticate($username, $password);

            if ($found_user) {
                if ($found_user->permissions <= ACTIVE) {
                    // Found user has sufficient permissions
                    $session->login($found_user);
                    redirect_to("index.php");
                } else {
                    $session->add_error("User not verified. Use the link in the verification email.");
                }
            } else {
                // Failure
                $session->add_error("Username/password not found.");
            }
        }
    }
    include("../../includes/layouts/header_login.php");
?>
<div class="container">
    <h1>Login</h1>
    <?php echo $session->message(); ?>
    <?php echo $session->error(); ?>
    <form class="form-horizontal" action="login.php" method="post">
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