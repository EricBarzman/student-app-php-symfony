<?php

namespace App\Controller;

use App\Entity\SchoolClass;
use Doctrine\Common\Collections\ArrayCollection;
use Dompdf\Dompdf;
use App\Form\SchoolClassType;
use App\Repository\SchoolClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/schoolclass')]
class SchoolClassController extends AbstractController
{
    #[Route('/index/{page}', name: 'app_school_class_index', methods: ['GET'])]
    public function index(SchoolClassRepository $schoolClassRepository, Request $request, int $page, int $resultPerPage = 10): Response
    {
        // Search
        $searchData = [
            'year' => $request->get('year'),
            'teacher_name' => $request->get('teacherName'),
        ];

        $classes = $schoolClassRepository->findByParameters($searchData);
        // $classes = $schoolClassRepository->findAllOrderedByYear();
        $numberClasses = count($classes);
        

        $collection = new ArrayCollection($classes);
        $collection = $collection->slice(($page - 1) *$resultPerPage, $resultPerPage);
        $totalPages = (int)ceil($numberClasses / $resultPerPage);

        return $this->render('schoolclass/index.html.twig', [
            'school_classes' => $collection,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/index/search/', name: 'app_school_class_search_index', methods: ['GET'])]
    public function search_index(SchoolClassRepository $schoolClassRepository, Request $request): Response
    {
        // Search
        $searchData = [
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'studentID' => $request->get('studentID'),
            'classID' => $request->get('classID')
        ];

        $classes = $schoolClassRepository->findByParameters($searchData);
        
        return $this->render('student/index.html.twig', [
            'school_classes' => $classes,
            'currentPage' => 1,
            'totalPages' => 1
        ]);
    }

    #[Route('/new', name: 'app_school_class_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolClass = new SchoolClass();
        $form = $this->createForm(SchoolClassType::class, $schoolClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($schoolClass);
            $entityManager->flush();

            return $this->redirectToRoute('app_school_class_show', array('id' => $schoolClass->getId()));
        }

        return $this->render('schoolclass/new.html.twig', [
            'school_class' => $schoolClass,
            'form' => $form,
        ]);
    }

    #[Route('/file/{id}', name: 'app_school_class_show', methods: ['GET'])]
    public function show(SchoolClass $schoolClass): Response
    {
        return $this->render('schoolclass/show.html.twig', [
            'school_class' => $schoolClass,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_school_class_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolClass $schoolClass, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolClassType::class, $schoolClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_school_class_show', array('id' => $schoolClass->getId()));
        }

        return $this->render('schoolclass/edit.html.twig', [
            'school_class' => $schoolClass,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_school_class_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolClass $schoolClass, int $id, EntityManagerInterface $entityManager): Response
    {
        if ($schoolClass->getStudents()->count() > 0)
            return $this->redirectToRoute('app_school_class_edit', ['id' => $id, 'message' => 'Cannot delete class with students!']);

        if ($this->isCsrfTokenValid('delete'.$schoolClass->getId(), $request->request->get('_token'))) {
            $entityManager->remove($schoolClass);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_class_index', ['page' => 1], Response::HTTP_SEE_OTHER);
    }

    // Imprimer par HTML
    #[Route('/print-cards/{id}', name: 'app_school_class_print_all_cards', methods: ['GET'])]
    public function print_all_student_cards(SchoolClass $schoolClass): Response
    {
        $students = $schoolClass->getStudents();
        $className = $schoolClass->getClassName();
        
        return $this->render('schoolclass/cardslist_HTML.html.twig', [
            'school_class' => $schoolClass,
            'students' => $students,
            'className' => $className
        ]);
    }

    /* IMPRIMER PAR PDF

    #[Route('/print-cards/{id}', name: 'app_school_class_print_all_cards', methods: ['GET'])]
    public function print_all_student_cards(SchoolClass $schoolClass): Response
    {
        $students = $schoolClass->getStudents();
        $className = $schoolClass->getClassName();
        
        $html = $this->renderView('schoolclass/cardslist_PDF.html.twig', [
            'school_class' => $schoolClass,
            'students' => $students
        ]);
        
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->SetTitle("Students class $className");
        
        return new Response (
            $mpdf->Output()
        );
    }
    */
}
