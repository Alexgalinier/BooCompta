<?php

class Coworker
{
    public static function checkRightsOn($coworkerId)
    {
        $coworker = Database::query('SELECT * FROM boocompta_coworker WHERE id = '.$coworkerId.' AND fk_user = '.App::getLoggedUserId());
        if ($coworker && $coworker === true) {
            App::noRightsTry();
        }
    }
    
    public static function getRemplaCoworker()
    {
        return Database::query('SELECT * FROM boocompta_coworker WHERE is_rempla = 1 AND fk_user = '.App::getLoggedUserId().' ORDER BY name', null, false);
    }
    
    public static function getCollabCoworker()
    {
        return Database::query('SELECT * FROM boocompta_coworker WHERE is_collab_assoc = 1 AND fk_user = '.App::getLoggedUserId().' ORDER BY name', null, false);
    }
    
    public static function getByPrestationId($id)
    {
        return Database::query(
            'SELECT c.* FROM boocompta_coworker c 
            INNER JOIN boocompta_prestation p ON p.fk_coworker = c.id
            WHERE p.id = '.$id
        );
    }
    
    public static function getByPaymentId($id)
    {
        return Database::query(
            'SELECT c.* FROM boocompta_coworker c 
            INNER JOIN boocompta_payment p ON p.fk_coworker = c.id
            WHERE p.id = '.$id
        );
    }
}