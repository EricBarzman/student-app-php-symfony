<?php

namespace App\Controller;

use App\Entity\SchoolClass;
use App\Entity\Timetable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/timetable')]
class TimetableController extends AbstractController
{
    public function createEmptyTimetable() {
        $timetable = [];
        $week = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        foreach($week as $day) {
            $emptyDay = [];
            for ($i = 8; $i < 19; $i++) {
                $emptyDay["{$i}"] = '';
            }
            $timetable[$day] = $emptyDay;
        }
        return $timetable;
    }
    
    #[Route('/show/{id}', name: 'app_timetable_show', methods: ['GET'])]
    public function show(SchoolClass $schoolclass, int $id): Response
    {
        $timetable = $schoolclass->getTimetable();

        if ($timetable == [] || $timetable->getTimetable() == [])
            return $this->redirectToRoute('app_timetable_create_form', [ 'id' => $id]);

        return $this->render('timetable/show.html.twig', [
            'school_class' => $schoolclass,
            'timetable' => $timetable->getTimetable()
        ]);
        
    }

    #[Route('/new/{id}', name: 'app_timetable_create_form', methods: ['GET'])]
    public function create_form(SchoolClass $schoolclass): Response
    {
        return $this->render('timetable/create.html.twig', [
            'timetable' => $this->createEmptyTimetable(),
            'school_class' => $schoolclass
        ]);
    }

    #[Route('/new/{id}', name: 'app_timetable_create_submit', methods: ['POST'])]
    public function create_submit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $timetable = new Timetable;
        $schoolclass = $entityManager->getRepository(SchoolClass::class)->find($id);

        $newTimetableToFill = $this->createEmptyTimetable();

        // Fill new timetable with values from request
        $week = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        foreach($week as $day) {
            $arrayDuJour = $newTimetableToFill[$day];
            foreach($arrayDuJour as $hour => &$content) {
                $content = $request->request->get($day.'_'.$hour);
            }
            $newTimetableToFill[$day] = $arrayDuJour;
        }

        // Ne fonctionnait pas en prod

        // foreach($newTimetableToFill as $day => $dayContent) {
        //     foreach($dayContent as $hour => &$hourContent) {
        //         $hourContent = $request->request->get($day.'_'.$hour);
        //     }
        //     $newTimetableToFill[$day] = $dayContent;
        // }

        $timetable->setTimetable($newTimetableToFill);
        $timetable->setSchoolClass($schoolclass);

        $entityManager->persist($timetable);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_timetable_show', [ 'id' => $id ]);
    }

    #[Route('/edit/{id}', name: 'app_timetable_edit_submit', methods: ['POST'])]
    public function edit_submit(SchoolClass $schoolclass, Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $timetableClass = $schoolclass->getTimetable();

        // Create a new timetable, fill it with new values
        $newTimetableToFill = $this->createEmptyTimetable();
        foreach($newTimetableToFill as $day => $dayContent) {
            foreach($dayContent as $hour => &$hourContent) {
                $hourContent = $request->request->get($day.'_'.$hour);
            }
            $newTimetableToFill[$day] = $dayContent;
        }

        $timetableClass->setTimetable($newTimetableToFill);
        
        $entityManager->persist($timetableClass);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_timetable_show', [ 'id' => $id ]);
    }

    #[Route('/edit/{id}', name: 'app_timetable_edit_form', methods: ['GET'])]
    public function edit_form(SchoolClass $schoolclass): Response
    {
        return $this->render('timetable/edit.html.twig', [
            'timetable' => $schoolclass->getTimetable()->getTimetable(),
            'school_class' => $schoolclass
        ]);
    }

    #[Route('/{id}', name: 'app_timetable_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, SchoolClass $schoolClass, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolClass->getId(), $request->request->get('_token'))) {
            
            $timetable = $schoolClass->getTimetable();
            $entityManager->remove($timetable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_class_show', [ 'id' => $id ]);
    }
}
