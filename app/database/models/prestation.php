<?php

class Prestation {
    
    public static function getByDate($coworkerId, $isRempla = true, $isPaid = false, $order = '')
    {
        return static::getAll($coworkerId, $isRempla, $isPaid, true, $order);
    }
    
    public static function getAll($coworkerId, $isRempla = true, $isPaid = false, $date = false, $order = 'ORDER BY patient_name ASC, date ASC')
    {
        Coworker::checkRightsOn($coworkerId);
        
        if ($isRempla) {
            $type = 'is_rempla = 1';
        } else {
            $type = 'is_collab_assoc = 1';
        }
        
        if ($isPaid) {
            $paidCond = 'AND (is_paid = 1 OR (is_paid_mutuel = 1 AND is_paid_cpam = 1))';
        } else {
            $paidCond = 'AND (is_paid <> 1 AND (is_paid_mutuel <> 1 OR is_paid_cpam <> 1))';
        }
        
        $dateCond = '';
        if ($date) {
            $dateCond = 'AND date like \''.$_SESSION['year'].'-'.$_SESSION['month'].'-%\'';
        }

        return Database::query(
            'SELECT * FROM boocompta_prestation 
            WHERE '.$type.' 
            '.$paidCond.'
            AND fk_coworker = '.$coworkerId.'
            '.$dateCond.'
            '.$order, null, false
        );
    }
    
    public static function getByPaymentId($id)
    {
        return Database::query('SELECT * FROM boocompta_prestation WHERE fk_payment = '.$id);
    }
    
    public static function get($id)
    {
        return Database::query('SELECT * FROM boocompta_prestation WHERE id = '.$id);
    }
    
    public static function getSumAmount($coworkerId, $isRempla = true)
    {
        if ($isRempla) {
            $type = 'is_rempla = 1';
        }
        
        return Database::query(
            'SELECT SUM(amount) as total_paid FROM boocompta_prestation 
            WHERE '.$type.' AND date like \''.$_SESSION['year'].'-'.$_SESSION['month'].'-%\'
            AND fk_coworker = '.$coworkerId
        );
    }
    
    public static function getSumAllAmount($coworkerId, $isRempla = true)
    {
        if ($isRempla) {
            $type = 'is_rempla = 1';
        }
        
        return Database::query(
            'SELECT SUM(amount) as amount FROM boocompta_prestation 
            WHERE '.$type.'
            AND fk_coworker = '.$coworkerId
        );
    }
    
    public static function delete($id)
    {
        $coworker = Coworker::getByPrestationId($id);
        
        Coworker::checkRightsOn($coworker->id);
        
        return Database::query('DELETE FROM boocompta_prestation WHERE id = '.$id);
    }
    
    public static function save($who, $date, $patientName, $amount, $isRempla = false, $isCollabAssoc = false)
    {
        Coworker::checkRightsOn($who);
        
        $data = array(
            ':fk_coworker' => $who,
            ':date' => Database::formatDateForm($date),
            ':patient_name' => $patientName,
            ':amount' => Database::formatAmountForm($amount),
            ':is_rempla' => $isRempla,
            ':is_collab_assoc' => $isCollabAssoc
        );
        
        Database::query(
            'INSERT INTO boocompta_prestation (fk_coworker,date,patient_name,amount,is_rempla,is_collab_assoc) 
                VALUES (:fk_coworker,:date,:patient_name,:amount,:is_rempla,:is_collab_assoc)',
            $data
        );
    }
    
    public static function paid($id, $paymentId)
    {
        Database::query('UPDATE boocompta_prestation SET is_paid = 1, fk_payment = '.$paymentId.' WHERE id = '.$id);
    }
    
    public static function paidMutuel($id, $paymentId)
    {
        Database::query('UPDATE boocompta_prestation SET is_paid_mutuel = 1, fk_payment_mutuel = '.$paymentId.' WHERE id = '.$id);
    }
    
    public static function paidCpam($id, $paymentId)
    {
        Database::query('UPDATE boocompta_prestation SET is_paid_cpam = 1, fk_payment_cpam = '.$paymentId.' WHERE id = '.$id);
    }
    
    public static function removePaid($paymentId)
    {
        Database::query('UPDATE boocompta_prestation SET is_paid = 0, fk_payment = 0 WHERE fk_payment = '.$paymentId);
    }
    
    public static function removePaidMutuel($paymentId)
    {
        Database::query('UPDATE boocompta_prestation SET is_paid_mutuel = 0, fk_payment_mutuel = 0 WHERE fk_payment_mutuel = '.$paymentId);
    }
    
    public static function removePaidCpam($paymentId)
    {
        Database::query('UPDATE boocompta_prestation SET is_paid_cpam = 0, fk_payment_cpam = 0 WHERE fk_payment_cpam = '.$paymentId);
    }
}
