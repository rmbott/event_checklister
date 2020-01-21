<?php

//require_once('library/initialize.php');

class UserController extends DefaultController
{
    public $model = null;
	public $session = null;

    public function __construct($session)
    {
		$this->session = $session;
        $this->model = null;
    }

    public function login($value = 0)
    {
        
		// Avoid multiple logins
		if ($this->session->is_logged_in()) {
			redirect_to("index.php");
		}
		
		// Validation
		if (isset($_POST['submit'])) {

			
			$required_fields = ['username', 'password'];
			validate_presences($required_fields);

			if ($this->session->error_check()) {

				$username = trim($_POST['username']);
				$password = trim($_POST['password']);

				$this->model = User::authenticate($username, $password);

				if ($this->model) {
					if ($this->model->permissions <= ACTIVE) {
						// Found user has sufficient permissions
						$this->session->login($this->model);
						$model = $this->model;
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

        include_once(VIEWS_PATH.DS.'login.php');
    }
}