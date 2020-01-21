<?php

/**
 * Copyright (c) 2014 Keith Casey
 *
 * This code is designed to accompany the lynda.com video course "Design Patterns in PHP"
 *   by Keith Casey. If you've received this code without seeing the videos, go watch the
 *   videos. It will make way more sense and be more useful in general.
 */
require_once('../../library/initialize.php');

$action = isset($_GET['a']) ? $_GET['a'] : 'index';
$module = isset($_GET['m']) ? $_GET['m'] : '';
$id     = isset($_GET['id']) ? $_GET['id'] : '';

switch($module) {
    case 'number':
        $controller = new NumberController();
        break;
	case 'user':
		$controller = new UserController($session);
		break;
		
    default:
        $controller = new DefaultController();
}

$controller->run($action, $id);