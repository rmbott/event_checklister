<?php

/*
 * Session class - For somewhat persistent data like user logins, messages, 
 * and errors.
 * 
 */
class Session {

	private $logged_in = FALSE;
	public $user_id;

	// Start the session
	function __construct() {
		session_start();
		$this->check_login();
	}

	// User Authentication Section

	public function is_logged_in() {
		return $this->logged_in;
	}

	public function login($user) {
		if ($user) {
			$this->user_id = $_SESSION['user_id'] = $user->id;
			$this->logged_in = TRUE;
		}
	}

	public function logout($user) {
		unset($_SESSION['user_id']);
		unset($this->user_id);
		$this->logged_in = FALSE;
	}

	private function check_login() {
		if (isset($_SESSION['user_id'])) {
			$this->user_id = $_SESSION['user_id'];
			$this->logged_in = TRUE;
		} else {
			unset($this->user_id);
			$this->logged_in = FALSE;
		}
	}

	// Errors & Messeges Section

	public function add_message($message) {
		$_SESSION["message"][] = $message;
	}

	public function message() {
		$output = "";
		if (isset($_SESSION["message"])) {
			if (!is_array($_SESSION["message"])) {
				$output .= $this->format($_SESSION["message"], "message");
			} elseif (is_array($_SESSION["message"])) {
					$output .= $this->format($_SESSION["message"], "message");
			}
			// clear message after use
			$_SESSION["message"] = null;

			return $output;
		}
	}

	public function add_error($error) {
		$_SESSION["error"][] = $error;
	}

	public function error() {
		$output = "";
		if (isset($_SESSION["error"])) {
			if (!is_array($_SESSION["error"])) {
				$output .= $this->format($_SESSION["error"], "error");
			} elseif (is_array($_SESSION["error"])) {
					$output .= $this->format($_SESSION["error"], "error");
				
			}
			// clear error after use
			$_SESSION["error"] = null;

			return $output;
		}
	}

	// A helper function to format messages or errors
	private function format($message, $type) {
		if (!is_array($message) && ($type == "error" || $type == "message")) {
			$output = "<div class=\"" . $type . "\"><ul><li>";
			$output .= htmlentities($message);
			$output .= "</li></ul></div>";
			return $output;
		}
		if (is_array($message) && ($type == "error" || $type == "message")) {
			$output = "<div class=\"" . $type . "\"><ul>";
			foreach ($message as $m) {
				$output .= "<li>" . htmlentities($m) . "</li>";
			}
			$output .= "</ul></div>";
			return $output;
		}
	}
	
	public function error_check() {
		return empty($_SESSION['error']);
	}

}
$session = new Session();
?>