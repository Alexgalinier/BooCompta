<?php

class Rempla
{
    const PRESTATION_PERCENT = 60;
    
    public static function handle()
    {
        App::checkLogged();
        
        if (App::request('who') && App::request('date') && App::request('patient_name') && App::request('amount')) {
            Prestation::save(App::request('who'), App::request('date'), App::request('patient_name'), App::request('amount'), true);
            View::data('message', 'Prestation le '.App::request('date').' du montant '.App::request('amount').'€ sauvée');
        }
        
        View::data('export_type', 'rempla');
        View::data('header_selected', 'rempla');
        
        static::setViewAndSessionData();
        
        View::data('remplas', array());
        View::data('prestations', array());
        
        $remplas = Coworker::getRemplaCoworker();
        $prestations = array();
        foreach ($remplas as $rempla) {
            $remplaPrestations = Prestation::getByDate($rempla->id, true, false, 'ORDER BY date, id DESC');
            if ($remplaPrestations !== true) {
                $prestations[$rempla->name] = $remplaPrestations;
            }
        }

        View::data('remplas', $remplas);
        View::data('prestations', $prestations);
        View::data('totalsPaid', static::getRecap());
        
        View::set('rempla', 'content');
        View::display();
    }
    
    public static function getRecap()
    {
        $totalsPaid = array();
        $remplas = Coworker::getRemplaCoworker();
        foreach ($remplas as $rempla) {
            $totalPaid = Prestation::getSumAmount($rempla->id);
            if ($totalPaid !== true) {
                $totalsPaid[$rempla->name] = array(
                    'total_paid' => round($totalPaid->total_paid, 2),
                    'total_to_rempla' => round($totalPaid->total_paid * (100 - Rempla::PRESTATION_PERCENT) / 100, 2),
                    'total_real' => round($totalPaid->total_paid * Rempla::PRESTATION_PERCENT / 100, 2),
                );
            }
        }
        
        return $totalsPaid;
    }
    
    public static function getFullSalary()
    {
        $salary = 0;
        
        $remplas = Coworker::getRemplaCoworker();
        foreach ($remplas as $rempla) {
            $prestationsAmount = Prestation::getSumAllAmount($rempla->id);
            if ($prestationsAmount !== true) {
                $salary += $prestationsAmount->amount;
            }
        }
        
        return $salary * Rempla::PRESTATION_PERCENT / 100;
    }
    
    private static function setViewAndSessionData()
    {
        if (App::request('who')) {
            $_SESSION['rempla'] = array();
            $_SESSION['rempla']['current_rempla'] = App::request('who');
            $_SESSION['rempla']['date'] = App::request('date');
            $_SESSION['rempla']['patient_name'] = App::request('patient_name');
            $_SESSION['rempla']['amount'] = App::request('amount');
            
            View::data('current_rempla', App::request('who'));
            View::data('date', App::request('date'));
            View::data('patient_name', App::request('patient_name'));
            View::data('amount', App::request('amount'));
        } else if (isset($_SESSION['rempla']['current_rempla'])) {
            View::data('current_rempla', $_SESSION['rempla']['current_rempla']);
            View::data('date', $_SESSION['rempla']['date']);
            View::data('patient_name', $_SESSION['rempla']['patient_name']);
            View::data('amount', $_SESSION['rempla']['amount']);
        } else {
            View::data('current_rempla', '');
            View::data('date', '');
            View::data('patient_name', '');
            View::data('amount', '');
        }
    }
}
