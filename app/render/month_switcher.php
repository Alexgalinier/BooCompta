<?php

class MonthSwitcher
{
    public static function display($currentMonth, $currentYear)
    {
        $months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $years = array();
        for($i = 2011; $i < 2020; $i++) {
            $years[] = $i;
        }
        
        $currentRoute = App::getCurrentRoute();
        
        $selectMonth = '<select id="month-switcher-month" name="month">';
        foreach($months as $val => $name) {
            if (($val + 1) == $currentMonth) {
                $selectMonth .= '<option value="'.($val + 1).'" selected="selected">'.$name.'</option>';
            } else {
                $selectMonth .= '<option value="'.($val + 1).'">'.$name.'</option>';
            }
        }
        $selectMonth .= '</select>';
        
        $selectYear = '<select id="month-switcher-year" name="year">';
        foreach($years as $val) {
            if ($val == $currentYear) {
                $selectYear .= '<option value="'.$val.'" selected="selected">'.$val.'</option>';
            } else {
                $selectYear .= '<option value="'.$val.'">'.$val.'</option>';
            }
        }
        $selectYear .= '</select>';
        
        echo '
            <div id="month-switcher">
                <a id="export" href="/'.$currentRoute.'/export/'.View::data('export_type').'">Exporter</a>
                <a href="/'.$currentRoute.'/prev_month"><img src="/images/prev-month.png" /></a>
                <form id="month-switcher-form" action="/'.$currentRoute.'/change_date" method="post">
                '.$selectMonth.'
                '.$selectYear.'
                </form>
                <a href="/'.$currentRoute.'/next_month"><img src="/images/next-month.png" /></a>
            </div>
            ';
    }
}
