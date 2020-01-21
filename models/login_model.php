<?php

//require_once("library/initialize.php");

class LoginModel
{
	public $session = null;
	
	public function __construct($session) {
		$this->session = $session;
	}
	
    public function check_logged_in()
    {
		if ($this->session->is_logged_in()) {
			redirect_to("index.php");
		}
    }
	
	public function validate_login() {
		if (isset($_POST['submit'])) {

			// Validation
			$required_fields = ['username', 'password'];
			validate_presences($required_fields);

			if ($this->session->error_check()) {

				$username = trim($_POST['username']);
				$password = trim($_POST['password']);

				$found_user = User::authenticate($username, $password);

				if ($found_user) {
					if ($found_user->permissions <= ACTIVE) {
						// Found user has sufficient permissions
						$this->session->login($found_user);
						redirect_to("index.php");
					} else {
						$this->session->add_error("User not verified. Use the link in the verification email.");
					}
				} else {
					// Failure
					$this->session->add_error("Username/password not found.");
				}
			}
		}
	}
}