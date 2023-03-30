<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\StudentRepository;
use App\Entity\Student;
use App\Form\ScannerType;
use App\Form\StudentType;
use App\Repository\RunRepository;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use App\Entity\Run;
use App\Service\CrossPetioHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
class ScannerController extends AbstractController
{
    #[Route('/{id}/scanner', name: 'app_scanner')]
    public function index(Run $run, Request $request,StudentRepository $studentRepository, RunRepository $runRepository, $id, CrossPetioHelper $crossPetioHelper): Response
    {
        
        $DateTime = new DateTime();
        $form = $this->createForm(ScannerType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $barcode = $form->get('barcode')->getData();
            $student = $studentRepository->findAll();
            foreach($student as $students){
                $run = $students->getRun();
                if($students->getBarcode()==$barcode && $run != null){
                    
                    if($run->getId() == $id)
                        {

                        $students->setEndrace($DateTime);
                        $mark = $crossPetioHelper->compute($students, $run);
                        $students->setMark($mark);
                        

                        $studentRepository->save($students, true);
                        return $this->renderForm('scanner/index.html.twig', [
                            'run' => $run,
                            'form' => $form,
                        ]);
                    }
                
                    else if ($students->getRun()->getId() != $id){
                        throw new HttpException(Response::HTTP_BAD_REQUEST, 'L\'utilisateur n\'existe pas ou ne fait pas partit de la course.');
                    }
                
                }

                
            }
            
        

        }
    
        return $this->renderForm('scanner/index.html.twig', [
        'run' => $run,
        'form' => $form,
    ]); 
    }
    

}
