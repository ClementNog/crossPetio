<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RunRepository;
use App\Repository\StudentRepository;
use App\Entity\Run;
use App\Form\FilterType;
use DateTime;

class RunController extends AbstractController
{
    #[Route('/run', name: 'app_run')]
    public function index(RunRepository $runRepository): Response
    {
        return $this->render('run/index.html.twig', [
            'controller_name' => 'RunController',
            'run' => $runRepository->findAll(),
        ]);
    }

    #[Route('/run/start', name: 'app_run_start', methods: ['GET', 'POST'])]
    public function start(Request $request, RunRepository $runRepository, StudentRepository $studentRepository): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        date_default_timezone_set("Europe/Paris");
        $start_string = date("Y:m:d H:i:s");
        $run = new Run();
        $run->setStart(new DateTime());
        $runRepository->save($run, true);
        $runid = $run->getId();
        $allStudent = $studentRepository->findAll(); 
        if ($form->isSubmitted() && $form->isValid()) {
            $grade = $form->get('grade')->getData();
            $gender = $form->get('gender')->getData();
            dump($gender);
            $level = $form->get('level')->getData();
            if ($grade->getShortname() == '0 Null' && $level != null)
            {
                    // dump($level);
                if($gender == "G"){
                    foreach($studentRepository->findAll() as $students){
                        $studlevel = $students->getGrade()->getLevel();
                        dump($student->getGender());
                        if( $studlevel == $level && $students->getGender()== "G"){
                            
                            $students->setRun($run);
                            dump($students);
                            $studentRepository->save($students, true);
                            
                            
                        }
                    }
                }
                else if($gender == "F"){
                    foreach($studentRepository->findAll() as $students){
                        $studlevel = $students->getGrade()->getLevel();
                        if( $studlevel == $level && $students->getGender() == "F"){
                            
                            $students->setRun($run);
                            $studentRepository->save($students, true);
                            
                        }
                    }
                }
                else {
                    foreach($studentRepository->findAll() as $students){
                        $studlevel = $students->getGrade()->getLevel();
                        if( $studlevel == $level){
                            
                            $students->setRun($run);
                            $studentRepository->save($students, true);                            
                        }
                    }
                }
                

            }
            else if ($grade->getShortname() == '0 Null' && $level == null)
            {
                if($gender == "G"){
                    foreach($studentRepository->findAll() as $students){
                        if($students->getGender() == "G"){
                            
                            $students->setRun($run);
                            $studentRepository->save($students, true);
                            
                        }
                    }
                }
                else if($gender == "F"){
                    foreach($studentRepository->findAll() as $students){
                        if($students->getGender() == "F"){
                            
                            $students->setRun($run);
                            $studentRepository->save($students, true);
                            
                        }
                    }
                }
                else {
                    foreach($studentRepository->findAll() as $students){
                            $students->setRun($run);
                            $studentRepository->save($students, true);
                    }
                }
                
            }
            else if ($grade->getShortname()!= "0 Null"){
                if($gender == "G"){
                    foreach($studentRepository->findAll() as $students){
                        $studlevel = $students->getGrade()->getId();
                        if( $studlevel == $grade->getId() && $students->getGender()== "G"){
                            
                            $students->setRun($run);
                            $studentRepository->save($students, true);
                            
                        }
                    }
                }
                else if($gender == "F"){
                    foreach($studentRepository->findAll() as $students){
                        $studlevel = $students->getGrade()->getId();
                        if( $studlevel == $grade->getId() && $students->getGender() == "F"){
                            
                            $students->setRun($run);
                            $studentRepository->save($students, true);
                            
                        }
                    }
                }
                else {
                    foreach($studentRepository->findAll() as $students){
                        $studlevel = $students->getGrade()->getId();
                        if( $studlevel == $grade->getId){
                            
                            $students->setRun($runid);
                            $studentRepository->save($students, true);                            
                        }
                    }
                }
                
                
            }
            return $this->render('run/start.html.twig', [
                'time' => $start_string,
                'id' => $runid,
            ]);
        }
        return $this->renderForm('run/new.html.twig', [
            'form' => $form,
            
        ]);
    }

}
