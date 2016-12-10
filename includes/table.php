<?php

    /*
     * Table Class - HTML tables for displaying objects which model database
     * tables (e.g. Users, Events, Contacts, DashboardElements).
     * 
     */
    
    class Table {

        public $rows;
        public $cols;
        private $output = "";
        private $header = TRUE;

        // Build the table
        public function __construct($object = NULL, $header = TRUE) {
            global $session;
            $this->rows = 0;
            $this->cols = 0;
            $this->output = "";
            $this->header = $header;
            if (empty($object)) {
                // This message should be dynamic-based upon $object type not about 
                // "events" only.
                $this->output .= "<div id=\"no_events\">No events exist. Try \"Create Event\" to create some.</div>";
            } else {
                if ($header) {
                    $this->add_header($object);
                }
                if (is_array($object)) {
                    foreach ($object AS $obj) {
                        $this->add($obj);
                    }
                } else {
                    $this->add($object);
                }
            }
        }

        // Returns the HTML table
        public function display() {
            // $this->output is not modified here. So, no need to worry about 
            // redundant table tags from calling display() more than once.
            return "<div class=\"dashboard-table\"><table class=\"table-responsive\">" . $this->output . "</table></div>";
        }

        // Add a row to the table
        public function add($object = NULL) {
            if ($object != NULL) {
                $row = "<tr><td><input type=\"checkbox\" name=\"" . $object->id . "\" form=\"dashboard\"></td>";
                $count = 0;
                $row_id = 0;
                foreach ($object AS $key => $value) {
                    // These time, date, phone checks improve the 
                    // DashboardElement table formatting. 
                    // Format time: h:mm XM
                    if ($key == "time") {
                        $row .= "<td>" . date('g:ia', strtotime($value)) . "</td>";

                        // Prevent word-wrap on dashes
                    } elseif ($key == "date") {
                        $row .= "<td><span class=\"nowrap\">" . $value . "</span></td>";
                        // Prevent word-wrap on dashes
                    } elseif (stristr($key, "phone")) {
                        $row .= "<td><span class=\"nowrap\">" . $value . "</span></td>";

                        // Everything else    
                    } else {
                        $row .= "<td>" . $value . "</td>";
                    }
                    $count++;
                }
                $row .= "</tr>";
                $this->output .= $row;
                $this->cols = $count;
                $this->rows++;
            }
        }

        // Add column headers
        private function add_header($object) {
            $h_row = "<tr><th></th>";
            is_array($object) ? $obj = array_shift($object) : $obj = $object;
            foreach ($obj AS $key => $value) {
                $h_row .= "<th>" . fieldname_as_text($key) . "</th>";
            }
            $h_row .= "</tr>";
            $this->output = $h_row;
            //$this->rows++;          Uncomment to count header row as a row
        }
    }