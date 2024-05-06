<?php

namespace App\Controller;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app')]
class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        if ($request->get('messageText')) $messageText = $request->get('messageText');   
        return $this->render('search/search.html.twig', [
            'messageText' => isset($messageText) ? $messageText : null
        ]);
    }

    #[Route('/search', name: 'app_search_id', methods: ['POST'])]
    public function search_id(EntityManagerInterface $doctrine, Request $request): Response
    {
        $studentId = $request->request->get("student_id");
        $student = $doctrine->getRepository(Student::class)->findOneByStudentId($studentId);
        if ($student) {
            $targetId = $student->getId();
            return $this->redirectToRoute('app_student_show', [ 'id' => $targetId]);
        }
        return $this->redirectToRoute('app_search', [ 'messageText' => 'No student found with this ID']);
    }
}