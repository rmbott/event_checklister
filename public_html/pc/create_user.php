<?php require_once("../../includes/initialize.php");

    /*
     * Creates a inactive user from the following form-submitted fields: email, 
     * username, and password. Uses ajax to indicate whether a the username is 
     * available or not. Sends a verification email with a URL to activate the 
     * user.
     * 
     */

    if (isset($_POST['submit'])) {

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);

        // form validation
        $required_fields = ['email', 'username', 'password'];
        validate_presences($required_fields);

        $max_lengths = ['username' => 32, 'password' => 32];
        validate_max_lengths($max_lengths);

        validate_email($email);

        if (empty($errors)) {

            // encrypt password and generate verification hash
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $hash = md5(uniqid(mt_rand(), true));
            
            // Create User
            $user = new User();
            $user->username = $username;
            $user->password = $hashed_password;
            $user->email = $email;
            $user->verificationhash = $hash;

            // Add inactive user to the database
            if ($user->create()) {
                $session->add_message("User created.");
                if ($user->send_verification_email()) {
                    $session->add_message("A verification email has been sent to " . htmlentities($email) . ". Click the link in that email to activate your new account. Check your spam folder if your email has not arrived.");
                } else {
                    $session->add_error("Verification email error.");
                }
            } else {
                $session->add_message("User creation failed.");
            }
            
            redirect_to("login.php");
        }
    }

    include("../../includes/layouts/header.php");
?>
<div class="container">
    <h1>Create user</h1>
    <?php echo $session->message(); ?>
    <?php echo $session->error(); ?>

    <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="email">Email</label>
            <div class="col-sm-10">
                <input class="form-control"  type="text" name="email" id="email" value="<?php echo keep_form_field('email'); ?>">
            </div>
            <div class="" id="user_sec">
                <label class="col-sm-2 control-label" for="username">Pick a username</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="username" id="username" value="<?php echo keep_form_field('username'); ?>">
                </div></div><span id="user-availability-status"></span><p><img src="LoaderIcon.gif" id="loaderIcon" style="display:none" /></p>
            <label class="col-sm-2 control-label" for="password">Pick a password</label>
            <div class="col-sm-10">
                <input class="form-control" type="password" name="password" id="password" value="<?php echo keep_form_field('password'); ?>">
            </div>
            <div class="col-sm-12">
                <input class="btn btn-default pull-right" type="submit" name="submit" value="Continue">
                <input class="btn btn-default pull-right" type="reset">
            </div>
        </div>
    </form>
    <script>

        // Reset form, since keep_form_field() broke the html reset-button.
        $(":reset").click(function () {
            $("#username").val("");
            $("#password").val("");
            $("#email").val("");
        });

        // See if a username is available
        $("#username").blur(function () {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "get_ajax_check_availability.php",
                data: 'username=' + $("#username").val(),
                type: "POST",
                success: function (data) {
                    $("#user-availability-status").html(data);
                    $("#loaderIcon").hide();
                    if ($('span.status-not-available').length) {
                        $("#user_sec").attr("class", "has-error");
                    } else if ($('span.status-available').length) {
                        $("#user_sec").attr("class", "has-success");
                    }
                },
                error: function () {}
            });
        });

    </script>

    <h1>or ...</h1>
    <div class="col-sm-10 col-sm-offset-2">
        <a href="login.php">Login</a>
    </div>
</div>
</body>
</html>