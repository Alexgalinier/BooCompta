<?php

require PATH_ROUTES . DS . 'Collab.php';
require PATH_ROUTES . DS . 'Rempla.php';

class Dashboard
{
    public static function handle()
    {
        App::checkLogged();
        
        View::data('export_type', 'main');
        View::data('header_selected', 'main');
        
        static::handlePost();
        static::setViewAndSessionData();
        
        $recapMonth = array();
        $remplaMonthRecap = Rempla::getRecap();
        foreach($remplaMonthRecap as $name => $rempla) {
            $recapMonth[$name.' (remplacement)'] = $rempla['total_real'];
        }
        $collabMonthRecap = Collab::getRecap();
        foreach($collabMonthRecap as $name => $rempla) {
            $recapMonth[$name.' (collaboration)'] = $rempla['total_real'];
        }
        
        View::data('recap', $recapMonth);
        
        View::data('fullSalary', Collab::getFullSalary() + Rempla::getFullSalary());
        
        $chargesAmount = Charge::getAllAmount(Charge::TYPE_CHARGE);
        if ($chargesAmount !== true) {
            View::data('chargesAmount', $chargesAmount->amount);
        } else {
            View::data('chargesAmount', 0);
        }
        
        $extrasAmount = Charge::getAllAmount(Charge::TYPE_EXTRA);
        if ($extrasAmount !== true) {
            View::data('extrasAmount', $extrasAmount->amount);
        } else {
            View::data('extrasAmount', 0);
        }
        
        View::data('keepForChargesPercent', App::getLoggedUser()->tax_percent_on_salary);
        View::data('keepForCharges', round((View::data('extrasAmount') + View::data('fullSalary')) * App::getLoggedUser()->tax_percent_on_salary / 100, 2));
        View::data('charges', Charge::getAll(Charge::TYPE_CHARGE));
        View::data('extras', Charge::getAll(Charge::TYPE_EXTRA));
        
        View::set('dashboard', 'content');
        View::display();
    }
    
    private static function handlePost()
    {
        if (App::request('add_charge')) {
            if (App::request('date') && App::request('name') && App::request('amount')) {
                Charge::save(App::request('date'), Charge::TYPE_CHARGE, App::request('name'), App::request('amount'));
                View::data('message', 'Charge du '.App::request('date').' de '.App::request('amount').'€ sauvée');
            }
        }
        
        if (App::request('add_extra')) {
            if (App::request('date') && App::request('name') && App::request('amount')) {
                Charge::save(App::request('date'), Charge::TYPE_EXTRA, App::request('name'), App::request('amount'));
                View::data('message', 'Extra du '.App::request('date').' de '.App::request('amount').'€ sauvé');
            }
        }
    }
    
    private static function setViewAndSessionData()
    {
        if (App::request('date')) {
            $_SESSION['dashboard'] = array();
            $_SESSION['dashboard']['date'] = App::request('date');
            $_SESSION['dashboard']['name'] = App::request('patient_name');
            $_SESSION['dashboard']['amount'] = App::request('amount');
            
            View::data('date', App::request('date'));
            View::data('name', App::request('name'));
            View::data('amount', App::request('amount'));
        } else if (isset($_SESSION['dashboard']['date'])) {
            View::data('date', $_SESSION['dashboard']['date']);
            View::data('name', $_SESSION['dashboard']['name']);
            View::data('amount', $_SESSION['dashboard']['amount']);
        } else {
            View::data('date', '');
            View::data('name', '');
            View::data('amount', '');
        }
    }
}
