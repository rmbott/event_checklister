<?php
//require_once(LIB_PATH.DS.'initialize.php');
/**
 * 
 */

class DefaultController
{
    public function run($action = 'index', $id = 0, $session)
    {
        if (!method_exists($this, $action)) {
            $action = 'index';
        }

        return $this->$action($id, $session);
    }

    public function index($session)
    {
        include VIEWS_PATH.DS.'default.php';
    }
}