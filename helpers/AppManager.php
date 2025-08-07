<?php

require_once 'PersistanceManager.php';
require_once 'SessionManager.php';

// Application manager
class AppManager
{

    private static $pm; // Persistance manager
    private static $sm; // Session manager

    // get persistance manager
    public static function getPM()
    {
        if (self::$pm === null) {
            self::$pm = new PersistanceManager();
        }
        return self::$pm;
    }

    // get session manager
    public static function getSM()
    {
        if (self::$sm === null) {
            self::$sm = new SessionManager();
        }
        return self::$sm;
    }
}
