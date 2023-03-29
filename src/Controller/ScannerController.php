<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\StudentRepository;
use App\Entity\Student;
use App\Form\ScannerType;
use App\Form\StudentType;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class ScannerController extends AbstractController
{
    #[Route('/scanner', name: 'app_scanner')]
    public function index(Request $request,StudentRepository $studentRepository): Response
    {

        $DateTime = new DateTime();
        $form = $this->createForm(ScannerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $barcode = $form->get('barcode')->getData();
            $student = $studentRepository->findAll();
            foreach($student as $students){
                if($students->getBarcode()==$barcode){
                    $students->setEndrace($DateTime);
                    $studentRepository->save($students, true);
                    return $this->renderForm('scanner/index.html.twig', [
                        'form' => $form,
                    ]); 
                }
            }
            
        


        }
        return $this->renderForm('scanner/index.html.twig', [

        'form' => $form,
    ]); 
    }
    
}
