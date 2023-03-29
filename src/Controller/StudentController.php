<?php

namespace App\Controller;

use TCPDF;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use DateTime;
use App\Repository\GradeRepository;
use App\Entity\Grade;
use App\Service\CrossPetioHelper;

#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'app_student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    #[Route('/barcode', name: 'app_student_barcode', methods: ['GET', 'POST'])]
    public function generatebarcode(StudentRepository $studentRepository, CrossPetioHelper $crossPetioHelper): Response
    {
        $barcode="";

        $user = $studentRepository->findAll();
        foreach ($studentRepository->findAll() as $key => $stud ) {
            $id = $stud->getId();
            if ($id < 10){
                $barcode = "00" . $id;
            }
            else if ($id <100){
                $barcode = "0" . $id;
            }
            else{
                $barcode = $id;
            }
            $stud->setBarcode($barcode);
            $test = $studentRepository->save($stud, true);
        

        }   
            return $this->renderForm('student/index.html.twig', [
                'students' => $studentRepository->findAll(),
            ]);
        }

        #[Route('/barcode/pdf', name: 'app_student_barcodepdf', methods: ['GET', 'POST'])]
        public function barcodepdf(StudentRepository $studentRepository): Response
        {
            $pdf = new \TCPDF;
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Nogueire Clement');
            $pdf->SetTitle('TCPDF Example 027');
            $pdf->SetSubject('TCPDF');
            $pdf->SetKeywords('TCPDF, PDF, example');
            
            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 027', PDF_HEADER_STRING);
            
            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            
            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            
            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            
            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            
            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
            }
            
            // ---------------------------------------------------------
            
            // set a barcode on the page footer
            $pdf->setBarcode(date('Y-m-d H:i:s'));
            
            // set font
            $pdf->SetFont('helvetica', '', 11);
            
            // -----------------------------------------------------------------------------
            
            
            // define barcode style
            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => true,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4,
                'clmargin',
                'multicell' => 0


            );
            $style2 = array(
                'position' => 'R',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => true,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4
            );
            
            // PRINT VARIOUS 1D BARCODES
            $students = $studentRepository->findAll();
            
            $pdf->AddPage();
            $cpt=0;
            foreach ($students as $student){
            // CODE 39 AUTO
                $barcode = $student->getBarcode();
                $pdf->Cell(0, 0, $barcode, 0, 1);
                $cpt++;
                // if ($cpt <= 4)
                // {
                    
                $pdf->write1DBarcode($barcode, 'C39', '', '', '', 18, 0.4, $style2, 'N');
                // }
                // else if ($cpt > 4)    
                // {
                // $x = 20;
                // $pdf->write1DBarcode($barcode, 'C39', '', '', '', 18, 0.4, $style, 'N');
                
                // }
                if($cpt == 8){
                    $pdf->addPage();
                    $cpt=0;
                    $x = 20;
                }

                
                $pdf->Ln();
            }
            return $pdf->output('barcode.pdf');
            
                }
    
    #[Route('/{id}', name: 'app_student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, StudentRepository $studentRepository): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $studentRepository->save($student, true);

            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, StudentRepository $studentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $studentRepository->remove($student, true);
        }

        return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
    }
}
