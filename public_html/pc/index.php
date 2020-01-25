<?php

/**
 * App Landing
 */
 
require_once('../../library/initialize.php');

$action = isset($_GET['a']) ? $_GET['a'] : 'index';
$module = isset($_GET['m']) ? $_GET['m'] : '';
$id     = isset($_GET['id']) ? $_GET['id'] : '';

switch($module) {

	case 'user':
		$controller = new UserController($session);
		break;
    default:
        $controller = new DefaultController($session);
}

$controller->run($action, $id, $session);