<?php

class Charge
{
    const TYPE_CHARGE = 'charge';
    const TYPE_EXTRA = 'extra';
    
    public static function getAll($type)
    {
        return Database::query(
            'SELECT * FROM boocompta_charge 
            WHERE type = \''.$type.'\'
            AND fk_user = '.App::getLoggedUserId().'
            ORDER BY date DESC, id DESC', null, false
        );
    }
    
    public static function getAllAmount($type)
    {
        return Database::query(
            'SELECT SUM(amount) as amount FROM boocompta_charge 
            WHERE type = \''.$type.'\'
            AND fk_user = '.App::getLoggedUserId()
        );
    }
    
    public static function save($date, $type, $name, $amount)
    {
        Database::query(
            'INSERT INTO boocompta_charge (fk_user,type,date,name,amount) 
                VALUES (:fk_user,:type,:date,:name,:amount)',
            array(
                ':fk_user' => App::getLoggedUserId(),
                ':type' => $type,
                ':date' => Database::formatDateForm($date),
                ':name' => $name,
                ':amount' => $amount
            )
        );
    }
    
    public static function delete($id)
    {
        return Database::query('DELETE FROM boocompta_charge WHERE fk_user = '.App::getLoggedUserId().' AND id = '.$id);
    }
}
