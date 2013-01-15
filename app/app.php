<?php

class App
{
    //Time in minutes
    const SESSION_DURATION = 60;
    
    private static $defautRoute = '/dashboard';

    public static function init()
    {
        mb_internal_encoding('UTF-8');

        //Start session
        session_start();
        
        //Set default month and year if not defined
        if (!isset($_SESSION['month'])) {
            $_SESSION['month'] = date('m');
            $_SESSION['year'] = date('Y');
        }

        //Connect database
        require PATH_DATABASE . DS . 'database.php';
        Database::init();
        
        //Get the view
        require PATH_RENDER.DS.'view.php';
        View::init('_base');
        
        require PATH_VENDORS.DS.'PHPExcel'.DS.'Classes'.DS.'PHPExcel.php';
    }

    public static function route()
    {
        $currentRoute = App::request('route');
        if ($currentRoute && file_exists(PATH_ROUTES . DS . $currentRoute . '.php') === true) {
            require PATH_ROUTES . DS . $currentRoute . '.php';
            $currentRoute::handle();
            exit();
        }

        if (static::checkLogged()) {
            static::defaultRoute();
        }

        echo 'No route for this request';
    }
    
    public static function getCurrentRoute()
    {
        return mb_strtolower(App::request('route'));
    }

    public static function request($paramName)
    {
        return (isset($_REQUEST[$paramName])) ? $_REQUEST[$paramName] : null;
    }

    public static function redirect($url)
    {
        header('Location: http://' .$_SERVER['SERVER_NAME'].'/'. $url);
        exit();
    }

    public static function defaultRoute()
    {
        static::redirect(static::$defautRoute);
    }

    public static function login($user)
    {
        $_SESSION['logged'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['created'] = time();
    }

    public static function logout()
    {
        $_SESSION = array();
    }
    
    public static function noRightsTry()
    {
        static::logout();
        echo "Don't mess with the dragon !!!!";
        exit();
    }
    
    public static function getLoggedUser()
    {
        return $_SESSION['user'];
    }
    
    public static function getLoggedUserId()
    {
        if (static::isLoggedUserAdmin()) {
            return $_SESSION['user']->administrated_user_id;
        } else {
            return $_SESSION['user']->id;
        }
    }
    
    public static function isLoggedUserAdmin()
    {
        return $_SESSION['user']->administrated_user_id != '0';
    }

    public static function checkLogged()
    {
        if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
            View::set('login', 'content');
            View::display();
            exit();
        }
        
        if ((time() - $_SESSION['created']) > App::SESSION_DURATION * 60) {
            static::logout();
            static::redirect('/');
        }

        return true;
    }

    public static function isLocal()
    {
        return $_SERVER['SERVER_NAME'] === 'boocompta.local';
    }
}
