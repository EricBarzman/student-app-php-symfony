<?php

namespace App\Controller;

use App\Entity\HistoryStudent;
use DateTime;
use App\Utils\Utils;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\SchoolClassRepository;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/student')]
class StudentController extends AbstractController
{
    public function __construct(private Utils $utils, private HttpClientInterface $client) {}


    // Interdit l'accès à l'index étudiant si pas admin - Deny access to student index if not an admin
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/index/{page}', name: 'app_student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository, SchoolClassRepository $schoolClassRepository, int $page, int $resultPerPage = 15): Response
    {
        $students = $studentRepository->findAll();
        $numberStudents = count($students); 
        
        $collection = new ArrayCollection($students);
        $collection = $collection->slice(($page - 1) *$resultPerPage, $resultPerPage);
        $totalPages = (int)ceil($numberStudents / $resultPerPage);

        return $this->render('student/index.html.twig', [
            'students' => $collection,
            'numberStudents' => "Number of students in school: ".$numberStudents.".",
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'classes' => $schoolClassRepository->findAll()
        ]);
    }


    // Interdit l'accès à l'index étudiant si pas admin - Deny access to student index if not an admin
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/index/search/{page}/', name: 'app_student_search_index', methods: ['GET'])]
    public function search_index(Request $request, StudentRepository $studentRepository, SchoolClassRepository $schoolClassRepository, int $page, int $resultPerPage = 15): Response
    {
        // Search
        $searchData = [
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'studentID' => $request->get('studentID'),
            'classID' => $request->get('classID')
        ];

        $foundStudents = $studentRepository->findByParameters($searchData);
        $numberStudents = count($foundStudents); 
        
        return $this->render('student/index.html.twig', [
            'students' => $foundStudents,
            'numberStudents' => "Results: ".$numberStudents." students found.",
            'currentPage' => $page,
            'totalPages' => 1,
            'classes' => $schoolClassRepository->findAll()
        ]);
    }


    #[Route('/class/{id}', name: 'app_student_list_by_class', methods: ['GET'])]
    public function listByClass(SchoolClassRepository $schoolClassRepository, StudentRepository $studentRepository, int $id): Response
    {
        $schoolClass = $schoolClassRepository->find($id);
        return $this->render('student/classIndex.html.twig', [
            'students' => $studentRepository->findBySchoolClass($id),
            'school_class' => $schoolClass
        ]);
    }


    #[Route('/create', name: 'app_student_create', methods: ['GET', 'POST'])]
    public function new(Request $request,
        EntityManagerInterface $entityManager,
        StudentRepository $studentRepository,
        SchoolClassRepository $schoolClassRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        $student = new Student();  
        $schoolClasses = $schoolClassRepository->findAll();
        
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Generate a new ID (auto incremental from the latest one)
            $latestStudentId = $studentRepository->findLargestStudentId();
            $student->setStudentId($this->utils->generateNewStudentIdFromLatestId($latestStudentId));

            // Create transfer
            $transfer = $this->createTransfer($student);

            // Upload photo
            $photo64string = $request->request->get('photo');            
            if ($photo64string) {
                $fileName = $this->utils->uploadPhotoFromString($photo64string);
                $student->setPhotoPath($fileName);
            }         
            // Save student
            $entityManager->persist($student);
            $entityManager->persist($transfer);
            $entityManager->flush();
            return $this->redirectToRoute('app_student_show', ['id' => $student->getId()]);
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form,
            'classes' => $schoolClasses
        ]);
    }


    #[Route('/file/{id}', name: 'app_student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        // Calculate age
        $dob = $student->getBirthdate();
        $year = $dob->diff(new DateTime());
        
        // Find current and next class today
        $currentClass = $this->utils->retrieveCurrentClass($student);
        $nextClass = $this->utils->retrieveNextClass($student)[0];
        $nextClassTime = $this->utils->retrieveNextClass($student)[1];

        return $this->render('student/show.html.twig', [
            'student' => $student,
            'age' => $year,
            'displayCurrentClass' => $currentClass ? date("ga").": ".$currentClass : '',
            'displayNextClass' => $nextClass ? $nextClassTime.": ".$nextClass : ''
        ]);
    }


    #[Route('/printQR/{id}', name: 'app_student_print_QR', methods: ['GET'])]
    public function printQR(Student $student): Response
    {
        return $this->render('student/printQR.html.twig', [
            'student' => $student,
        ]);
    }


    #[Route('/edit/{id}', name: 'app_student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $form = $this->createForm(StudentType::class, $student, [
            'allow_extra_fields' => true
        ]);

        $oldClass = $form->getData()->getSchoolClass();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Check if there's a class transfer by comparing old class to new one
            $newClass = $form->getData()->getSchoolClass();
            if ($oldClass->getId() != $newClass->getId()) {
                $transfer = $this->createTransfer($student);
                $entityManager->persist($transfer);
            }

            // Upload photo
            $photo64string = $request->request->get('photo');            
            if ($photo64string) {
                $fileName = $this->utils->uploadPhotoFromString($photo64string);
                
                // Remove previous photo
                $previousPath = $student->getPhotoPath();
                if ($previousPath) {
                    if (file_exists('uploads/photos/'.$previousPath))
                        unlink('uploads/photos/'.$previousPath);
                }

                $student->setPhotoPath($fileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_student_show', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }


    #[Route('/delete/{id}', name: 'app_student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            
            // Remove photo
            $previousPath = $student->getPhotoPath();
            if ($previousPath) {
                if (file_exists('uploads/photos/'.$previousPath))
                    unlink('uploads/photos/'.$previousPath);
            }  

            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', ['message' => 'Student file deleted'], Response::HTTP_SEE_OTHER);
    }


    #[Route('/timetable/{id}', name: 'app_student_show_timetable', methods: ['GET'])]
    public function show_student_timetable(Student $student): Response
    {
        $schoolclass = $student->getSchoolClass();
        return $this->render('timetable/show_non-admin.html.twig', [
            'school_class' => $schoolclass,
            'id' => $student->getId(),
            'timetable' => $schoolclass->getTimetable()->getTimetable()
        ]);
    }

    
    /**
     * Take a student and create an instance of a transfer, ready to be persisted in the database
     */
    public function createTransfer(Student $student){
        $transfer = new HistoryStudent();

        $transfer->setStudentFirstname($student->getFirstname());
        $transfer->setStudentLastname($student->getLastname());
        $transfer->setStudentGender($student->getGender() == 1 ? 'boy' : 'girl');
        $transfer->setBirthdate($student->getBirthdate());
        $transfer->setClassName($student->getSchoolClass()->getClassName());
        $transfer->setPromotionYear($student->getSchoolClass()->getPromotionYear());
        $transfer->setMainTeacherName($student->getSchoolClass()->getTeacherName());
        $transfer->setDateTransfer(new DateTime());

        return $transfer;
    }
}
