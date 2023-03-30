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
            $sheet->setCellValue('G'.$cpt, $list->getMark());
            
            

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
        $note = 15;
        $studentobj = $student->getObjective();
        $departrace =  $run->getStart();
        $studentendrace = $student->getEndrace();
        $objectif = $departrace->add($departrace);
        dump($objectif);
        $comp = $objectif->diff($studentendrace);
        $minutes = (int) $comp->format('i');
        $seconde = (int) $comp->format('s');
        if($minutes == 0 && $seconde == 0) {
            return $note;
        }
        if($comp->invert==1)
        {
            if($minutes == 0 && $seconde>=1 && $seconde<15){
                return $note=14;
            }
            else if($minutes == 0 && $seconde>=15 && $seconde<30){
                return $note=13;
            }
            else if($minutes == 0 && $seconde>=30 && $seconde<45){
                return $note=12;
            }
            else if($minutes == 0 && $seconde>=45 && $seconde<60){
                return $note=11;
            }
            else if($minutes == 1 && $seconde>=1 && $seconde<15){
                return $note=10;
            }
            else if($minutes == 1 && $seconde>=15 && $seconde<30){
                return $note=9;
            }
            else if($minutes == 1 && $seconde>=30 && $seconde<45){
                return $note=8;
            }
            else if($minutes == 1 && $seconde>=45 && $seconde<60){
                return $note=7;
            }
            else if($minutes == 2 && $seconde>=1 && $seconde<15){
                return $note=6;
            }
            else if($minutes == 2 && $seconde>=15 && $seconde<30){
                return $note=5;
            }
            else if($minutes == 2 && $seconde>=30 && $seconde<45){
                return $note=4;
            }
            else if($minutes == 2 && $seconde>=45 && $seconde<60){
                return $note=3;
            }
            else if($minutes == 3 && $seconde>=1 && $seconde<15)
                return $note=2;
            
            else if($minutes == 2 && $seconde>=15 && $seconde<30)
                return $note=1;
            
            else return $note=0;
        }
        else{
            if($minutes == 0 && $seconde>=1 && $seconde<=10){
                return $note=16;
            }
            else if($minutes == 0 && $seconde>10 && $seconde<=20){
                return $note=17;
            }
            else if($minutes == 0 && $seconde>20 && $seconde<=30){
                return $note=18;
            }
            else if($minutes == 0 && $seconde>30 && $seconde<=40){
                return $note=19;
            }
            else if($minutes == 1 && $seconde>40 && $seconde<=50){
                return $note=20;
            }

        }

        

        }
        
    }