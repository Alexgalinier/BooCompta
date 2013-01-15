<?php

class Collab
{
    public static function handle()
    {
        App::checkLogged();
        
        //Set the first coworker as collab
        if (!App::request('who')) {
            if (isset($_SESSION['current_who'])) {
                App::redirect('collab/'.$_SESSION['current_who']);
            } else {
                $coworkers = Coworker::getCollabCoworker();
                if ($coworkers[0]) {
                    App::redirect('collab/'.$coworkers[0]->id);
                }
            }
        } else {
            $_SESSION['current_who'] = App::request('who');
        }
        
        static::handlePost();
        static::setViewAndSessionData();
        
        View::data('export_type', 'collab');
        View::data('header_selected', 'collab');
        
        $collabs = Coworker::getCollabCoworker();
        foreach($collabs as $collab) {
            $collab->unpaid_prestations = Prestation::getAll($collab->id, false);
            $collab->paid_prestations = Prestation::getByDate($collab->id, false, true, 'ORDER BY date, patient_name');
            $collab->payments = Payment::getByDate($collab->id);

            if ($collab->id == App::request('who')) {
                View::data('current', $collab);
            }
        }
        
        View::data('totalsPaid', static::getRecap());
        View::data('collabs', $collabs);
        
        View::set('collab', 'content');
        View::display();
    }
    
    public static function getRecap()
    {
        $totalsPaid = array();
        $collabs = Coworker::getCollabCoworker();
        foreach($collabs as $collab) {
            $collab->payments = Payment::getByDate($collab->id);
            
            $totalsPaid[$collab->name]['total_paid'] = 0;
            $totalsPaid[$collab->name]['total_to_collab'] = 0;
            if ($collab->payments !== true) {
                foreach($collab->payments as $payment) {
                    $totalsPaid[$collab->name]['total_paid'] += $payment->amount;
                    $totalsPaid[$collab->name]['total_to_collab'] += $payment->amount * (100 - $payment->percent) / 100;
                }
            }
            
            $totalsPaid[$collab->name]['total_paid'] = round($totalsPaid[$collab->name]['total_paid'], 2);
            $totalsPaid[$collab->name]['total_to_collab'] = round($totalsPaid[$collab->name]['total_to_collab'], 2);
            $totalsPaid[$collab->name]['total_real'] = $totalsPaid[$collab->name]['total_paid'] - $totalsPaid[$collab->name]['total_to_collab'];
        }
        
        return $totalsPaid;
    }
    
    public static function getFullSalary()
    {
        $salary = 0;
        $collabs = Coworker::getCollabCoworker();
        foreach($collabs as $collab) {
            $payments = Payment::getAll($collab->id);
            
            if ($payments !== true) {
                foreach($payments as $payment) {
                    $salary += $payment->amount * $payment->percent / 100;
                }
            }
        }
        
        return $salary;
    }
    
    private static function handlePost()
    {
        if (App::request('add_prestation')) {
            if (App::request('date') && App::request('patient_name') && App::request('amount') && App::request('percent')) {
                Prestation::save(App::request('who'), App::request('date'), App::request('patient_name'), App::request('amount'), false, true);
                View::data('message', 'Prestation le '.App::request('date').' du montant '.App::request('amount').'€ sauvée');
            }
        }
        
        if (App::request('add_payment_mutuel')) {
            if (App::request('date') && 
                    App::request('prestations') && is_array(App::request('prestations')) && sizeof(App::request('prestations')) > 0 && 
                    App::request('percent')) {
                Payment::save(App::request('date'), Payment::TYPE_MUTUEL, App::request('percent'), App::request('prestations'));
                View::data('message', 'Paiement le '.App::request('date').' sauvé');
            }
        }
        
        if (App::request('add_payment_cpam')) {
            if (App::request('date') && 
                    App::request('prestations') && is_array(App::request('prestations')) && sizeof(App::request('prestations')) > 0 && 
                    App::request('percent')) {
                Payment::save(App::request('date'), Payment::TYPE_CPAM, App::request('percent'), App::request('prestations'));
                View::data('message', 'Paiement le '.App::request('date').' sauvé');
            }
        }
        
        if (App::request('add_payment')) {
            if (App::request('date') && 
                    App::request('prestations') && is_array(App::request('prestations')) && sizeof(App::request('prestations')) > 0 && 
                    App::request('percent')) {
                Payment::save(App::request('date'), Payment::TYPE_FULL, App::request('percent'), App::request('prestations'));
                View::data('message', 'Paiement le '.App::request('date').' sauvé');
            }
        }
    }
    
    private static function setViewAndSessionData()
    {
        if (App::request('date')) {
            $_SESSION['collab'] = array();
            $_SESSION['collab']['date'] = App::request('date');
            $_SESSION['collab']['patient_name'] = App::request('patient_name');
            $_SESSION['collab']['amount'] = App::request('amount');
            $_SESSION['collab']['percent'] = App::request('percent');
            
            View::data('date', App::request('date'));
            View::data('patient_name', App::request('patient_name'));
            View::data('amount', App::request('amount'));
            View::data('percent',  App::request('percent'));
        } else if (isset($_SESSION['collab']['date'])) {
            View::data('date', $_SESSION['collab']['date']);
            View::data('patient_name', $_SESSION['collab']['patient_name']);
            View::data('amount', $_SESSION['collab']['amount']);
            View::data('percent', $_SESSION['collab']['percent']);
        } else {
            View::data('date', '');
            View::data('patient_name', '');
            View::data('amount', '');
            View::data('percent', 70);
        }
        
        View::data('percents', array(70,100));
    }
}
