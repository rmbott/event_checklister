<?php
    /*
     * Database Constants
     */
    
    //    // Deployment Version
    //    defined("DB_SERVER") ? NULL : define("DB_SERVER", "mydomain.com");
    //    defined("DB_USER") ? NULL : define("DB_USER", "ec_admin");
    //    defined("DB_PASS") ? NULL : define("DB_PASS", "secretpassword");
    //    defined("DB_NAME") ? NULL : define("DB_NAME", "ecdb");
    
    // Development Version
    defined("DB_SERVER") ? NULL : define("DB_SERVER", "localhost");
    defined("DB_USER") ? NULL : define("DB_USER", "ec_admin");
    defined("DB_PASS") ? NULL : define("DB_PASS", "secretpassword");
    defined("DB_NAME") ? NULL : define("DB_NAME", "ecdb");
    
    // Authentication
    define("INACTIVE", 5);
    define("ACTIVE", 4);
    define("PERM3", 3);
    define("PERM2", 2);
    define("ADMIN", 1);
    