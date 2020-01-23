<?php
    
    /*
     * Webroot and Library Path Constants
     * 
     * Defines site paths for human readable absolute references to included or 
     * required files. (eliminates all the "../../../../.. etc." relative paths)
     */
    
    // Directory separator
    defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
    
    // Deployment Version
    // 
    //    // Site Root Path
    //    defined('SITE_ROOT') ? null : 
    //            define('SITE_ROOT', DS.'home'.DS.'public_html'.DS.'pc');
    //    
    //    // Library (include files) Path
    //    defined('LIB_PATH') ? null : define('LIB_PATH', DS.'home'.DS.'includes');

    
    // Development Version
	
	// Show More Errors
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

    // Site Root Path
    defined('SITE_ROOT') ? null : define('SITE_ROOT', 'C:'.DS.'wamp64'.DS.'www'.DS.'event_checklister');
    
    // Library Path
    defined('LIB_PATH') ? null : define('LIB_PATH', 'C:'.DS.'wamp64'.DS.'www'.DS.'event_checklister'.DS.'library');
	
	// Controller Path
    defined('CONTROLLERS_PATH') ? null : define('CONTROLLERS_PATH', 'C:'.DS.'wamp64'.DS.'www'.DS.'event_checklister'.DS.'controllers');
	
	// Models Path
    defined('MODELS_PATH') ? null : define('MODELS_PATH', 'C:'.DS.'wamp64'.DS.'www'.DS.'event_checklister'.DS.'models');
	
	// Views Path
    defined('VIEWS_PATH') ? null : define('VIEWS_PATH', 'C:'.DS.'wamp64'.DS.'www'.DS.'event_checklister'.DS.'views');

	require_once(LIB_PATH.DS."functions.php");
	require_once(LIB_PATH.DS."session.php");
	require_once(CONTROLLERS_PATH.DS."default_controller.php");
	require_once(CONTROLLERS_PATH.DS."user_controller.php");
	require_once(MODELS_PATH.DS."user.php");
    require_once(LIB_PATH.DS."db_config.php");
    require_once(MODELS_PATH.DS."database.php");
    require_once(MODELS_PATH.DS."database_object.php");
    require_once(MODELS_PATH.DS."user.php");
    // require_once(LIB_PATH.DS."event.php");
    // require_once(LIB_PATH.DS."contact.php");
    // require_once(LIB_PATH.DS."table.php");
    // require_once(LIB_PATH.DS."dashboard_element.php");
    require_once(LIB_PATH.DS."validation_functions.php");
    // require_once(LIB_PATH.DS."event_set.php");