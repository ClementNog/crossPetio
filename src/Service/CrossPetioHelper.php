<?php

namespace App\Service;

use TCPDF;
use App\Entity\Student;
use App\Repository\StudentRepository;

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
}