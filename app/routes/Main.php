<?php

require PATH_ROUTES . DS . 'Rempla.php';

class Main
{

    public static function handle()
    {
        App::checkLogged();

        if (App::request('action')) {
            switch (App::request('action')) {
                case 'prev_month': 
                    static::prevMonth();
                    break;
                case 'next_month': 
                    static::nextMonth();
                    break;
                case 'change_date': 
                    static::changeDate();
                    break;
                case 'delete': 
                    static::deleteRessource(App::request('type'), App::request('id'));
                    break;
                case 'export': 
                    static::export(App::request('type'));
                    break;
            }
        }

        App::redirect(App::request('current_route'));
    }

    public static function deleteRessource($type, $id)
    {
        switch ($type) {
            case 'prestation':
                Prestation::delete($id);
                break;
            case 'payment':
                Payment::delete($id);
                break;
            case 'charge':
                Charge::delete($id);
                break;
        }
    }

    public static function prevMonth()
    {
        $newTime = mktime(0, 0, 0, $_SESSION['month'] - 1, 1, $_SESSION['year']);
        $_SESSION['month'] = date('m', $newTime);
        $_SESSION['year'] = date('Y', $newTime);
    }

    public static function nextMonth()
    {
        $newTime = mktime(0, 0, 0, $_SESSION['month'] + 1, 1, $_SESSION['year']);
        $_SESSION['month'] = date('m', $newTime);
        $_SESSION['year'] = date('Y', $newTime);
    }

    public static function changeDate()
    {
        if (App::request('month')) {
            $_SESSION['month'] = (App::request('month') < 10) ? '0'.App::request('month') : App::request('month');
        }
        
        if (App::request('year')) {
            $_SESSION['year'] = App::request('year');
        }
    }
    
    public static function export($type)
    {
        if ($type === 'collab') {
            //Nothing, let it redirect
        } elseif ($type === 'collab') {
            //Nothing, let it redirect
        } elseif ($type === 'rempla') {
            $excel = new PHPExcel();
            
            $remplas = Coworker::getRemplaCoworker();
            $sheetIndex = 0;
            foreach ($remplas as $rempla) {
                $remplaPrestations = Prestation::getByDate($rempla->id, true, false, 'ORDER BY date, id DESC');
                if ($remplaPrestations !== true) {
                    $sheet = $excel->createSheet($sheetIndex);
                    $sheet->setTitle($rempla->name);
                    
                    $sum = 0;
                    $currentDate = '';
                    $rowIndex = 1;
                    foreach($remplaPrestations as $presta) {
                        if ($currentDate !== $presta->date) {
                            $currentDate = $presta->date;
                            $rowIndex++;
                        }
                        
                        $sheet->setCellValue('A'.$rowIndex, $presta->date);
                        $sheet->setCellValue('B'.$rowIndex, $presta->patient_name);
                        $sheet->setCellValue('C'.$rowIndex, $presta->amount);
                        
                        $sum += $presta->amount;
                        
                        $rowIndex++;
                    }
                    
                    $rowIndex++;
                    $sheet->setCellValue('B'.$rowIndex, 'Total');
                    $sheet->setCellValue('C'.$rowIndex, $sum);
                    
                    $rowIndex++;
                    $sheet->setCellValue('B'.$rowIndex, 'Total '.Rempla::PRESTATION_PERCENT.'%');
                    $sheet->setCellValue('C'.$rowIndex, round($sum * Rempla::PRESTATION_PERCENT / 100, 2));
                    
                    //Define size
                    $sheet->getColumnDimensionByColumn('A')->setAutoSize(true);
                    $sheet->getColumnDimensionByColumn('B')->setAutoSize(true);
                    $sheet->getColumnDimensionByColumn('C')->setAutoSize(true);
                    
                    $sheetIndex++;
                }
            }
            
            $excel->setActiveSheetIndex(0);
            
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="remplacements_du_'.$_SESSION['year'].'_'.$_SESSION['month'].'.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
    }

}
