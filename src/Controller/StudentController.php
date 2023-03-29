<?php

namespace App\Controller;

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

    #[Route('/new', name: 'app_student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StudentRepository $studentRepository,SluggerInterface $slugger,GradeRepository $gradeRepository): Response
    {
        $message = "START";

        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $studentRepository->save($student, true);
            $brochureFile = $form->get('brochure')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('filenames_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $student->setBrochureFilename($newFilename);
               $serializer = new CsvEncoder();
                // decoding CSV contents
                $data = $serializer->decode(file_get_contents($this->getParameter('filenames_directory') . '/' . $student->getBrochureFilename()), 'csv');
                // $message .= "data = '" . print_r($data, true) . "'";

                foreach ($data as $key => $value) {
                    // $message .= "key = '" . print_r($key, true) . "'";
                    $student = new Student();
                    $student->setLastname($value['NUM']);
                    $student->setShortname($value['Prénom']);
                    $student->setLastname($value['Nom']);
                    $student->setGender($value['SEXE']);
                    $student->setMas(floatval($value['VMA']));
                    $student->setObjective(new DateTime()); //$value['TEMPS']

                    //$gradeShortname = substr($value['CLASSE'], 2);
                    $gradeShortname = $value['CLASSE'];
                    $gradeLevel = $value['CLASSE'][0];
                    $grade = $gradeRepository->findOneBy(array('shortname' => $gradeShortname));
                    if (!isset($grade)) {
                        $grade = new Grade();
                        $grade->setShortname($gradeShortname);
                        $grade->setLevel($gradeLevel);
                        $gradeRepository->save($grade, true);
                    }
        
                    $student->setGrade($grade);

                    $studentRepository->save($student, true);
                }

                //$request = "INSERT INTO student(id, shortname, lastname, grade_id, gender, mas, objective) VALUES('NUM', 'Nom', 'Prénom', 'CLASSE', 'SEXE', '', 'TEMPS')";

            }

            $studentRepository->save($student, true);

            return $this->render('student/index.html.twig', [
                'students' => $studentRepository->findAll(),
                'message' => $message,
            ]);
        }

        return $this->renderForm('student/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
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
