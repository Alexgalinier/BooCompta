<?php

class View
{
    private static $base = null;
    private static $templates = array();
    private static $data = array();

    public static function init($baseTemplate)
    {
        //Load helpers
        require PATH_RENDER.DS.'month_switcher.php';
        
        //Set base template
        static::base($baseTemplate);
    }

    public static function base($template)
    {
        static::$base = $template;
    }

    public static function set($template, $key)
    {
        static::$templates[$key] = $template;
    }

    public static function get($key)
    {
        if (isset(static::$templates[$key])) {
            include PATH_TEMPLATES . DS . static::$templates[$key] . '.php';
        }

        if (file_exists(PATH_TEMPLATES . DS . $key . '.php')) {
            include PATH_TEMPLATES . DS . $key . '.php';
        }
    }

    public static function data($key, $data = null)
    {
        if ($data !== null) {
            static::$data[$key] = $data;
        }

        if (isset(static::$data[$key])) {
            return static::$data[$key];
        }
        
        return null;
    }

    public static function display()
    {
        //Display base template
        include PATH_TEMPLATES . DS . static::$base . '.php';
    }
    
    public static function formatDate($date)
    {
        $explodedData = explode('-',$date);
        return $explodedData[2].'/'.$explodedData[1].'/'.$explodedData[0];
    }
    
    public static function formatAmount($amount)
    {
        return number_format($amount, 2, ',', ' ');
    }

}