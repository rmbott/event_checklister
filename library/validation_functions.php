<?php

    // Makes fieldnames easier to read 
    // e.g. "first_name" -> "First Name"
    function fieldname_as_text($fieldname) {
        $fieldname_without_underscores = str_replace("_", " ", $fieldname);
        $fieldname_as_text = ucfirst($fieldname_without_underscores);
        return $fieldname_as_text;
    }

    // A helper function for: validate_presences()
    // Validates whether a form field is completly empty. 
    function has_presence($value) {
        return isset($value) && $value !== "";
    }

    // Validates whether a form field is completly empty and generates 
    // appropriate errors. Useful for required form fields.
    // Parameters: 
    //      $required_fields - an array of fieldnames
    // 
    function validate_presences($required_fields) {
        global $session;

        foreach ($required_fields as $field) {
            $value = trim($_POST[$field]);
            if (!has_presence($value)) {
                $session->add_error(fieldname_as_text($field) . " can't be blank.");
            }
        }
    }

    // A helper function for: validate_disjunctive_presences()
    // Removes a redundant comma, making the errors look nicer.
    function remove_trailing_comma($string) {
        return str_lreplace($string, ",", "");
    }

    // Validates whether at least one of several form field is not empty and 
    // generates appropriate errors. Useful for a nonspecific requirement like 
    // requiring that the user enter some phone number but not caring whether 
    // they fill in the cell_phone field or the home_phone field.
    // Parameters: 
    //      $fields - an array of fieldnames
    // 
    function validate_disjunctive_presences($fields) {
        global $session;
        $none_present = TRUE;
        $error_name = "";
        foreach ($fields as $field) {
            $error_name .= fieldname_as_text($field) . ", ";
            $value = trim($_POST[$field]);
            if (has_presence($value)) {
                $none_present = FALSE;
            }
        }
        $error_name = remove_trailing_comma($error_name);
        if ($none_present) {
            $session->add_error(fieldname_as_text($error_name) . " can't all be blank.");
        }
    }

    // A helper function for: validate_max_lengths()
    // Validates whether a form field does not exceed a given length. 
    function has_max_length($value, $max_length) {
        return strlen($value) <= $max_length;
    }

    // Validates whether a form field does not exceed a given length and 
    // generates appropriate errors. Useful for preventing invalid database 
    // insertions due to size limitations.
    // Parameters: 
    //      $fields_with_max_lengths - an accociative array of fieldnames and
    //                                 corresponding mac character lengths
    // 
    function validate_max_lengths($fields_with_max_lengths) {
        global $session;

        foreach ($fields_with_max_lengths as $field => $max) {
            $value = trim($_POST[$field]);
            if (!has_max_length($value, $max)) {
                $session->add_error(fieldname_as_text($field) . " is too long.");
            }
        }
    }

    
    // Validates whether a form field contains a valid email and generates 
    // appropriate errors. Useful for preventing invalid database insertions due 
    // to size limitations.
    // Parameters: 
    //      $email - a string
    // 
    function validate_email($email) {
        global $session;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $session->add_error("Invalid email.");
        }
    }

?>