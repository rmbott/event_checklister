<?php require_once("../../includes/initialize.php");
    if (!$session->is_logged_in()) {
        redirect_to("login.php");
    }
    
    /*
     * Imports contacts from a .CSV file of the following format:
     *  _________________________________________________________________________________________________________________________________________________________
     * | First Name | Last Name | Email Address | Day Phone | Evening Phone | Cell Phone | Address Line 1 | Address Line 2 | Address Line 3 | City | State | Zip |
     * |------------|-----------|---------------|-----------|---------------|------------|----------------|----------------|----------------|------|-------|-----|
     * 
     */

    if (isset($_POST['submit'])) {
        $file_to_be_uploaded = $_FILES["file_to_upload"]["tmp_name"];
        $target_dir = "uploads/";
        $target_name = md5(uniqid(mt_rand(), true));
        $target_file = $target_dir . basename($target_name);

        if (move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file)) {
            $session->add_message("The file " . basename($_FILES["file_to_upload"]["name"]) . " has been uploaded.");
            $data = csv_to_assoc($target_file, true);
            $rows_inserted = 0;
            $errors = [];
            if (!is_array($data)) {
                $session->add_message("Sorry, there was an error uploading your file.");
            } else {
                foreach ($data as $row) {
                    $contact = new Contact();
                    foreach ($row AS $key => $value) {
                        // empty
                        if (empty($row[$key])) {
                            $contact->$key = NULL;
                        // Abbreviate State Names
                        } elseif ($key == "state" && !empty($value)) {
                            $contact->$key = two_digit_state($row[$key]);
                        } else {
                            $contact->$key = $value;
                        }
                    }
                    if ($contact->create()) {
                        $rows_inserted++;
                    } else {
                        $errors[] = $row["last_name"] . ", " . $row["first_name"] . " - could not be added. - " . $database->error() . ".";
                    }
                }
                $session->add_message($rows_inserted . " contacts imported successfuly");
                foreach ($errors AS $error) {
                    $session->add_error($error);
                }
            }
        }
    }
    include("../../includes/layouts/header.php");
?>
<div class="container">
    <h1>Import Contacts (CSV)</h1>
    <?php echo $session->message(); ?>
    <?php echo $session->error(); ?>
    <form class="form-horizontal" action="import_contacts.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <input class="btn btn-default" type="file" name="file_to_upload" id="file_to_upload" value="Choose .csv File">
            </div>
            <div class="col-sm-10 col-sm-offset-2">
                <input class="btn btn-default" type="submit" name="submit">
            </div>
        </div>
    </form>
</div>
</body>
</html>