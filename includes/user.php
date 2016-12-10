<?php

    require_once(LIB_PATH . DS . "database.php");

    /*
     * User class
     * 
     * Models a row of the users table in the database.
     */
    
    class User extends DatabaseObject {

        protected static $table_name = "users";
        protected static $fields = "*";
        protected static $sql_id = "id";
        protected static $sql_user_id = "id";
        protected static $form_name = "users";
        public $id;
        public $username;
        public $password;
        public $email;
        public $permissions;
        public $verificationhash;

        // User Authentication
        public static function authenticate($username = "", $plain_password = "") {
            global $database;
            $username = $database->escape_string_query($username);
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
            $password = $database->escape_string_query($hashed_password);

            $sql = "SELECT * FROM users ";
            $sql .= "WHERE username = '{$username}' ";
            $sql .= "LIMIT 1";

            $result_array = self::find_by_sql($sql);
            if (empty($result_array)) {
                // No such username found
                return FALSE;
            } else {
                // Password checks out
                $user = array_shift($result_array);
                if (password_verify($plain_password, $user->password)) {
                    return $user;
                } else {
                    // Password doesn't check out
                    return FALSE;
                }
            }
            
            // Consider removing, I'm not sure this is reachable
            return !empty($result_array) ? array_shift($result_array) : FALSE;
        }

        // Create User
        public function create() {
            global $database;
            $params = get_object_vars($this);
            $sql = "INSERT INTO " . self::$table_name . " ( ";
            $sql .= " username, password, email, verificationhash ";
            $sql .= " ) VALUES ( ";
            foreach ($params AS $key => $value) {
                
                //use SQL-default values for "id" and "permissions"
                if ($key == "id" || $key == "permissions") { continue; }
                
                $sql .= sql_quotes($database->escape_string_query($this->$key)) . ", ";
            }
            $sql = remove_trailing_comma($sql) . ")";
            
            if ($database->query($sql)) {
                //return the auto-increment "id"
                $this->id = $database->insert_id();
                return true;
            } else {
                return false;
            }
        }
        
        // Verification Email
        public function send_verification_email() {
        $subject = 'Verification Email - Events Checklister';
        $header = 'From:noreply@rmbott.com' . "\r\n";
        $body = '

        Thanks for signing up!
        Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.

        ------------------------
        Username: ' . $this->username . '
        ------------------------

        Please click this link to activate your account:
        http://rmbott.com/pc/activate_user.php?email=' . $this->email . '&hash=' . $this->verificationhash . '

        ';
        return mail($this->email, $subject, $body, $header);
    }

    }
    