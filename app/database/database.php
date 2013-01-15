<?php

class Database
{
    static $pdo;
    
    public static function init()
    {
        if (App::isLocal()) {
            $host = 'localhost';
            $user = 'root';
            $password = '';
        } else {
            $host = '?';
            $user = '?';
            $password = '?';
        }
        
        //Connect PDO
        static::$pdo = new PDO('mysql:dbname=alexgaliddhelp;'.$host, $user, $password);
        
        //Set utf8
        static::query("SET NAMES 'utf8'");
        
        //Get models
        require __DIR__.DS.'models'.DS.'prestation.php';
        require __DIR__.DS.'models'.DS.'coworker.php';
        require __DIR__.DS.'models'.DS.'payment.php';
        require __DIR__.DS.'models'.DS.'charge.php';
    }
    
    public static function query($request, $param = null, $returnObjIfOnlyOneFound = true)
    {
        if ($param === null) {
            $res = static::$pdo->query($request);
        } else {
            $res = static::$pdo->prepare($request);
            $res->execute($param);
        }
        
        $items = array();
        while(($item = $res->fetch(PDO::FETCH_OBJ))) {
            $items[] = $item;
        }
        
        if (sizeof($items) === 0) {
            return true;
        } elseif (sizeof($items) === 1 && $returnObjIfOnlyOneFound === true) {
            return $items[0];
        } else {
            return $items;
        }
    }
    
    public static function insertId()
    {
        return static::$pdo->lastInsertId();
    }
    
    public static function formatDateForm($date)
    {
        $explodedDate = explode('/', $date);
        return $explodedDate[2].'-'.$explodedDate[1].'-'.$explodedDate[0];
    }
    
    public static function formatAmountForm($amount)
    {
        return str_replace(',', '.', $amount);
    }
}