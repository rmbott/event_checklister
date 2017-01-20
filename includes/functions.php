<?php

    // Redirects to a given URL. This will cause an error if used on a page that
    // has already sent the header.
    function redirect_to($new_location) {
        header("Location: " . $new_location);
        exit;
    }

    // Retains HTML form values across submissions
    function keep_form_field($field_name) {
        if (isset($_POST[$field_name])) {
            return htmlentities(trim($_POST[$field_name]));
        } else {
            return "";
        }
    }

    // A helper function used by array_first_row_to_assoc_keys. Applies a crude 
    // heuristic for detecting changes to the format of .csv exported contacts. 
    function sanitize_pc_headers($pc_headers) {
        global $session;
        // Crude heuristic 
        if (count($pc_headers) == 12) {
            return ["first_name", "last_name", "email", "day_phone",
                "evening_phone", "cell_phone", "addr1", "addr2", "addr3",
                "city", "state", "zip"];
        } else {
            $session->add_error("PC has changed their db structure. The administrator has been contacted. Contact import will be unavailible while we make the appropriate changes.");
            return $pc_headers;
        }
    }

    // Takes a multi-dim array of rows where the first row is columns headers 
    // and returns an associative array with column headers as keys. 
    function array_first_row_to_assoc_keys($array) {
        $headers = array_shift($array);
        $sanitized_headers = sanitize_pc_headers($headers);
        foreach ($array as $row) {
            if (is_array($row)) {
                $assoc[] = array_combine($sanitized_headers, $row);
            }
        }
        return $assoc;
    }

    // Takes a csv file and returns an associative array if the first row is 
    // column headers, and an ordered array otherwise.
    function csv_to_assoc($filename, $headers = true) {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        } else {
            $file = fopen($filename, 'r');
            while ($rows[] = fgetcsv($file)) {
                
            }
            if ($headers) {
                return array_first_row_to_assoc_keys($rows);
            } else {
                return $rows;
            }
        }
    }

    function has_required_fields($array, $fields) {
        $a_field_is_missing = false;
        foreach ($fields AS $field) {
            if (empty($array[$field])) {
                $a_field_is_missing = true;
            }
        }
        return !$a_field_is_missing;
    }

    // Takes a U.S. state name and returns the USPS abbreviation.
    function two_digit_state($state) {
        switch ($state) {
            case "Alabama": return "AL";
            case "Alaska": return "AK";
            case "American Samoa": return "AS";
            case "Arizona": return "AZ";
            case "Arkansas": return "AR";
            case "California": return "CA";
            case "Colorado": return "CO";
            case "Connecticut": return "CT";
            case "Delaware": return "DE";
            case "District of Columbia": return "DC";
            case "Federated States of Micronesia": return "FM";
            case "Florida": return "FL";
            case "Georgia": return "GA";
            case "Guam": return "GU";
            case "Hawaii": return "HI";
            case "Idaho": return "ID";
            case "Illinois": return "IL";
            case "Indiana": return "IN";
            case "Iowa": return "IA";
            case "Kansas": return "KS";
            case "Kentucky": return "KY";
            case "Louisiana": return "LA";
            case "Maine": return "ME";
            case "Marshall Islands": return "MH";
            case "Maryland": return "MD";
            case "Massachusetts": return "MA";
            case "Michigan": return "MI";
            case "Minnesota": return "MN";
            case "Mississippi": return "MS";
            case "Missouri": return "MO";
            case "Montana": return "MT";
            case "Nebraska": return "NE";
            case "Nevada": return "NV";
            case "New Hampshire": return "NH";
            case "New Jersey": return "NJ";
            case "New Mexico": return "NM";
            case "New York": return "NY";
            case "North Carolina": return "NC";
            case "North Dakota": return "ND";
            case "Northern Mariana Islands": return "MP";
            case "Ohio": return "OH";
            case "Oklahoma": return "OK";
            case "Oregon": return "OR";
            case "Palau": return "PW";
            case "Pennsylvania": return "PA";
            case "Puerto Rico": return "PR";
            case "Rhode Island": return "RI";
            case "South Carolina": return "SC";
            case "South Dakota": return "SD";
            case "Tennessee": return "TN";
            case "Texas": return "TX";
            case "Utah": return "UT";
            case "Vermont": return "VT";
            case "Virgin Islands": return "VI";
            case "Virginia": return "VA";
            case "Washington": return "WA";
            case "West Virginia": return "WV";
            case "Wisconsin": return "WI";
            case "Wyoming": return "WY";
            default: return false;
        }
    }

    // Check the the LIB_PATH directory for a class, before throwing a class 
    // not found error. 
    //      (Note: use of __autoload() should be changed to  
    //      spl_autoload_register(). 
    //              see: http://php.net/manual/en/language.oop5.autoload.php)
    //
    function __autoload($class_name) {
        $class_name = strtolower($class_name);
        $lib = LIB_PATH . DS . "{$class_name}.php";
        $fpdf = LIB_PATH . DS . "FPDF" . DS . "{$class_name}.php";
        if (file_exists($lib)) {
            require_once($lib);
        } elseif (file_exists($fpdf)) {
            // Check the LIB_PATH/FPDF path too
            require_once($fpdf);
        } else {
            die("The file {$class_name}.php could not be found.");
        }
    }

    // like substr_replace() but does nothing if substring cannot be found
    function str_lreplace($string, $search, $replace) {
        $pos = strrpos($string, $search);

        if ($pos !== false) {
            $string = substr_replace($string, $replace, $pos, strlen($search));
        }
        return $string;
    }

    // Surrounds a string in single quotes if it non-numeric. Handy for SQL 
    // queries.
    function sql_quotes($string) {
        return (is_numeric($string)) ? $string : "'" . $string . "'";
    }

    /*
     * Replaces the last occuring "AND" with a ")" in a string. Useful for 
     * fixing SQL syntax.
     * 
     * @parameter String
     * @returns String
     */

    function replace_trailing_AND($string) {
        return str_lreplace($string, "AND", ")");
    }

    /*
     * Replaces the last occuring "OR" with a ")" in a string. Useful for 
     * fixing SQL syntax.
     * 
     * @parameter String
     * @returns String
     */

    function replace_trailing_OR($string) {
        return str_lreplace($string, "OR", ")");
    }
    