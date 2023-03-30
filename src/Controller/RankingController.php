<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\RunRepository;
use App\Service\CrossPetioHelper;
use App\Repository\StudentRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
                        if( $studlevel == $level && $students->getGender()== "M"){
                            
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
                        if($students->getGender() == "M"){
                            
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
                        if( $studlevel == $grade->getId() && $students->getGender()== "M"){
                            
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
                        if( $studlevel == $grade->getId){

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
    #[Route('/ranking/excel', name: 'app_rankingExel')]
    public function export(Request $request, StudentRepository $studentRepository){
       // Récupérer les données de la base de données à exporter
       $data = $studentRepository->findAll();

       // Créer un nouveau fichier Excel
       $spreadsheet = new Spreadsheet();

       $sheet = $spreadsheet->getActiveSheet();
       
       $sheet->setCellValue('A1', 'shortname')
            ->setCellValue('B1', 'lastname')
            ->setCellValue('C1', 'objective')
            ->setCellValue('D1', 'fin de course')
            ->setCellValue('E1', 'Note');

       // Remplir les données
       dump($sheet);
       $i = 2;
       foreach ($data as $row) {
       $sheet->setCellValue('A'.$i, $row->getShortname())
           ->setCellValue('B'.$i, $row->getLastname())
           ->setCellValue('C'.$i, $row->getObjective())
           ->setCellValue('D'.$i, $row->getEndrace())
           ->setCellValue('E'.$i,$row->getMark());
       $i++;
       }

       // Enregistrer le fichier Excel
       $writer = new Xlsx($spreadsheet);
       $filename = 'exportRace.xlsx';
       $writer->save($filename);

       // Retourner le fichier Excel
       $response = new BinaryFileResponse($filename);
       $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
       return $response;

    }
}
    

