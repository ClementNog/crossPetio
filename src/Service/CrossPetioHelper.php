<?php

namespace App\Service;

use TCPDF;
use App\Entity\Student;
use App\Repository\StudentRepository;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Entity\Run;
class CrossPetioHelper
{
    public function barcodepdf(StudentRepository $studentRepository)
    {
        $pdf = new TCPDF();
        $allstudent = $studentRepository->findAll();
        // Autres configurations du PDF ici
        $pdf->AddPage();
        $cpt1 = 0;
        $cpt2 = 0;
        foreach ($allstudent as $students){
            // Écriture du code-barres
            $cpt1++;
            $cpt2++;
            $barcode = $students->getBarcode();
            if ($cpt1 < 4)            $pdf->write1DBarcode($barcode, 'C39', '', '', '', 18, 0.4, '','', 'N');
            else if ($cpt1 >= 4)           $pdf->write1DBarcode($barcode, 'C39', '', '', '', 18, 0.4, '','', 'N');
            if ($cpt1 == 8){
                $cpt1 == 0;
                $pdf->AddPage();
            }
        }
    }
    public function makeExport(Spreadsheet $spreadsheet, $list){
        $cpt = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Barcode');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prénom');
        $sheet->setCellValue('D1', 'Sexe');
        $sheet->setCellValue('E1', 'VMA');
        $sheet->setCellValue('F1', 'Temps');
        $sheet->setCellValue('G1', 'Note');

        foreach ($list as $lists){
            $cpt++;
            $sheet->setCellValue('A'.$cpt, $list->getBarcode());
            $sheet->setCellValue('B'.$cpt, $list->getLastname());
            $sheet->setCellValue('C'.$cpt, $list->getShortname());
            $sheet->setCellValue('D'.$cpt, $list->getGender());
            $sheet->setCellValue('E'.$cpt, $list->getVma());
            $sheet->setCellValue('F'.$cpt, $list->getEndrace());
            // $sheet->setCellValue('G'.$cpt, $list->getMark());
            
            

        }
    }
    public function objective(Student $student)
    {
        $time = null;
        $mas = $student->getMas();
            if($mas != 0){
                $mas = $mas *0.8;
                $vitesse = $mas / 3.6;

                // calcul du temps en secondes
                $temps = 1500 / $vitesse;
        
                // affichage du temps en minutes et secondes
                $minutes = floor($temps / 60);
                $secondes = round($temps % 60);
                $time = date_create()->setTime(0, 0, round($temps));            }
        return $time;
    }
    public function compute(Student $student, Run $run)
    {
        $note = 0;
        $studentobj = $student->getObjective();
        $departrace =  $run->getStart();


        $studentendrace = $student->getEndrace();


        // $objectif = $departrace->add($studentobj->diff($departrace));
        // dump($objectif);
        // $comp = $objectif->diff($studentendrace);
        // $departrace = strtotime($departrace->format('i:s'));
        $tempsobj = $studentobj->getTimestamp() + $departrace->getTimestamp();
        dump($tempsobj);
        // $studentendrace = $studentendrace->getTimestamp();
        dump($studentendrace);
        // if($tempsobj == $studentendrace) {
        //     $note = 15;
        // }
        $minutesrace = $departrace->format('i');
        $secondesrace = $departrace->format('s');
        $minutesrace = $minutesrace*60;
        $secondesrace = $secondesrace + $minutesrace;

        
        $minutesobj = $studentobj->format('i');
        $secondesobj = $studentobj->format('s');
        $minutesobj = $minutesobj*60;
        $secondesobj = $secondesobj + $minutesobj;

        $secondesobjectif = $secondesobj + $secondesrace; 

        $minutesfin = $studentendrace->format('i');
        $secondesfin = $studentendrace->format('s');
        dump($minutesfin);
        dump($secondesfin);
        $minutesfin = $minutesfin*60;
        $secondesfin = $secondesfin + $minutesfin;
        dump($secondesobjectif);
        dump($secondesfin);
        $secondes = $secondesfin - $secondesobjectif;

        dump($secondes);
        if ($secondes> -15 && $secondes < 15){
            $notes = 15;
            dump($seconde);
        }
            if($secondes>=15 && $secondes<30){
                 $note=14;
            }
            else if($secondes>=30 && $secondes<45){
                 $note=13;
            }
            else if($secondes>=45 && $secondes<60){
                 $note=12;
            }
            else if($secondes>=60 && $secondes<75){
                 $note=11;
            }
            else if($secondes>=75 && $secondes<90){
                 $note=10;
            }
            else if($secondes>=90 && $secondes<105){
                 $note=9;
            }
            else if($secondes>=105 && $secondes<120){
                 $note=8;
            }
            else if($secondes>=120 && $secondes<135){
                 $note=7;
            }
            else if($secondes>=135 && $secondes<150){
                 $note=6;
            }
            else if($secondes>=150 && $secondes<165){
                 $note=5;
            }
            else if($secondes>=165 && $secondes<180){
                 $note=4;
            }
            else if($secondes>=180 && $secondes<195){
                 $note=3;
            }
            else if($secondes>=195 && $secondes<205)
                 $note=2;
            
            else if($secondes>=205 && $secondes<220)
                 $note=1;
            
            else $note=0;
        
        
        
            dump($secondes);
            if($secondes<=-1 && $secondes>=-10){
                 $note=16;
            }
            else if($secondes<-10 && $secondes>=-20){
                $note=17;
            }
            else if($secondes<-20 && $secondes>=-30){
                $note=18;
            }
            else if($secondes<-30 && $secondes>=-40){
                $note=19;
            }
            else if($secondes<-40){
                $note=20;
            }
            dump($note);
            return $note;
        
    }
        
}