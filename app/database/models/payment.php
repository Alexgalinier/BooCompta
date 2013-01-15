<?php

class Payment {
    
    const TYPE_MUTUEL = 'mutuel';
    const TYPE_CPAM = 'cpam';
    const TYPE_FULL = 'full';
    const PERCENT_MUTUEL = .4;
    const PERCENT_CPAM = .6;
    
    public static function getByDate($coworkerId)
    {
        Coworker::checkRightsOn($coworkerId);
        
        return Database::query(
            'SELECT * FROM boocompta_payment 
            WHERE fk_coworker = '.$coworkerId.' 
            AND date like \''.$_SESSION['year'].'-'.$_SESSION['month'].'-%\'
            ORDER BY date', null, false
        );
    }
    
    public static function getAll($coworkerId)
    {
        Coworker::checkRightsOn($coworkerId);
        
        return Database::query(
            'SELECT * FROM boocompta_payment 
            WHERE fk_coworker = '.$coworkerId.' 
            ORDER BY date', null, false
        );
    }
    
    public static function get($id)
    {
        return Database::query('SELECT * FROM boocompta_payment  WHERE id = '.$id);
    }
    
    public static function save($date, $type, $percent, $prestationIdList)
    {
        $coworker = Coworker::getByPrestationId($prestationIdList[0]);
        
        Coworker::checkRightsOn($coworker->id);
        
        $patientName = '';
        $amount = 0;
        foreach($prestationIdList as $prestationId) {
            $prestation = Prestation::get($prestationId);
            if ($patientName === '') {
                $patientName = $prestation->patient_name;
            }
            $amount += $prestation->amount;
        }
        
        switch ($type) {
        case Payment::TYPE_MUTUEL:
            $amount = round($amount * Payment::PERCENT_MUTUEL, 2);
            break;
        case Payment::TYPE_CPAM:
            $amount = round($amount * Payment::PERCENT_CPAM, 2);
            break;
        }
        
        Database::query(
            'INSERT INTO boocompta_payment (fk_coworker,date,type,patient_name,amount,percent) 
                VALUES (:fk_coworker,:date,:type,:patient_name,:amount,:percent)',
            array(
                ':fk_coworker' => $coworker->id,
                ':date' => Database::formatDateForm($date),
                ':type' => $type,
                ':patient_name' => $patientName,
                ':amount' => $amount,
                ':percent' => $percent
            )
        );
        
        $newPaymentId = Database::insertId();
        foreach($prestationIdList as $prestationId) {
            $prestation = Prestation::get($prestationId);
            
            //Save the prestation as paid for the type
            switch ($type) {
            case Payment::TYPE_MUTUEL:
                Prestation::paidMutuel($prestation->id, $newPaymentId);
                break;
            case Payment::TYPE_CPAM:
                Prestation::paidCpam($prestation->id, $newPaymentId);
                break;
            case Payment::TYPE_FULL:
                Prestation::paid($prestation->id, $newPaymentId);
                break;
            }
        }
    }
    
    public static function delete($id)
    {
        $coworker = Coworker::getByPaymentId($id);
        
        Coworker::checkRightsOn($coworker->id);
        
        $payment = static::get($id);
        if ($payment && is_object($payment)) {
            //Delete payment
            Database::query('DELETE FROM boocompta_payment WHERE id = '.$id);

            //Remove payment from prestation
            switch ($payment->type) {
            case Payment::TYPE_MUTUEL:
                Prestation::removePaidMutuel($payment->id);
                break;
            case Payment::TYPE_CPAM:
                Prestation::removePaidCpam($payment->id);
                break;
            case Payment::TYPE_FULL:
                Prestation::removePaid($payment->id);
                break;
            }
        }
    }
}