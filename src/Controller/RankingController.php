<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RunRepository;
use App\Form\FilterType;
use App\Service\CrossPetioHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RankingController extends AbstractController
{
    private $list = array();
    #[Route('/ranking', name: 'app_ranking')]
    public function ranking(Request $request,StudentRepository $studentRepository, RunRepository $runRepository): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);
        $list = array();

        if ($form->isSubmitted() && $form->isValid()) {
            $grade = $form->get('grade')->getData();
            $gender = $form->get('gender')->getData();
            $level = $form->get('level')->getData();
            if ($grade->getShortname() == '0 Null' && $level != null)
            {
                if($gender == "G"){
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        $studlevel = $students->getGrade()->getLevel();
                        if( $studlevel == $level && $students->getGender()== "G"){
                            
                        array_push($list, $students);

                            
                            
                        }
                    }
                }
                else if($gender == "F"){
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        $studlevel = $students->getGrade()->getLevel();
                        if( $studlevel == $level && $students->getGender() == "F"){
                            
                        array_push($list, $students);

                            
                        }
                    }
                }
                else {
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        $studlevel = $students->getGrade()->getLevel();
                        if( $studlevel == $level){
                            
                            array_push($list, $students);
                            
                        }
                    }
                }
                

            }
            else if ($grade->getShortname() == '0 Null' && $level == null)
            {
                if($gender == "G"){
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        if($students->getGender() == "G"){
                            
                        array_push($list, $students);

                            
                        }
                    }
                }
                else if($gender == "F"){
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        if($students->getGender() == "F"){
                            
                        array_push($list, $students);

                            
                        }
                    }
                }
                else {
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){

                        
                        array_push($list, $students);

                    }
                }
                
            }
            else if ($grade->getShortname() != "0 Null"){
                if($gender == "G"){
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        $studlevel = $students->getGrade()->getId();
                        if( $studlevel == $grade->getId() && $students->getGender()== "G"){
                            
                            array_push($list, $students);

                            
                        }
                    }
                }
                else if($gender == "F"){
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        $studlevel = $students->getGrade()->getId();
                        if( $studlevel == $grade->getId() && $students->getGender() == "F"){


                            array_push($list, $students);


                            
                        }
                    }
                }
                else {
                    foreach($studentRepository->findBy([], ['endrace' => 'ASC']) as $students){
                        $studlevel = $students->getGrade()->getId();
                        if( $studlevel == $grade->getId()){

                            array_push($list, $students);
                            
                        }
                    }
                }
                
            }
            return $this->render('ranking/ranking-result.html.twig', [
                'level' => $level,
                'list' => $list,
            ]);
        }
        return $this->renderForm('ranking/ranking-form.html.twig', [
            'form' => $form,
            
        ]);
    }
    public function export(Spreadsheet $spreadsheet, CrossPetioHelper $crossPetioHelper){
        $crossPetioHelper->makeExport($spreadsheet, $list);

    }
}
    

