<?php
require_once("../../includes/initialize.php");
$session->logout($session->user_id);
redirect_to("login.php");

