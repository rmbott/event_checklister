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

    // Site Root Path
    defined('SITE_ROOT') ? null : 
            define('SITE_ROOT', DS.'home'.DS.'ryan'.DS.'public_html'.DS.'event_checklister'.DS.'public_html'.DS.'pc');
    
    // Library (include files) Path
    defined('LIB_PATH') ? null : define('LIB_PATH', DS.'home'.DS.'ryan'.DS.'public_html'.DS.'event_checklister'.DS.'includes');

    require_once(LIB_PATH.DS."db_config.php");
    require_once(LIB_PATH.DS."functions.php");
    require_once(LIB_PATH.DS."session.php");
    require_once(LIB_PATH.DS."database.php");
    require_once(LIB_PATH.DS."database_object.php");
    require_once(LIB_PATH.DS."user.php");
    require_once(LIB_PATH.DS."event.php");
    require_once(LIB_PATH.DS."contact.php");
    require_once(LIB_PATH.DS."table.php");
    require_once(LIB_PATH.DS."dashboard_element.php");
    require_once(LIB_PATH.DS."validation_functions.php");
?>