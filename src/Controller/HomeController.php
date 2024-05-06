<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_entry')]
    public function entry(): Response
    {
        if ($this->getUser()) return $this->redirectToRoute('app_home');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/app', name: 'app_home')]
    public function home(): Response
    {
        // Erase pincode for safety
        $this->getUser()->eraseCredentials();
        
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) return $this->redirectToRoute('app_admin_entry');
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('home/index.html.twig', [
            'message' => null
        ]);
    }

    #[Route('/admin', name:'app_admin_entry')]
    public function admin_home(): Response
    {
        // Erase pincode for safety
        $this->getUser()->eraseCredentials();
        
        return $this->render('home/admin.html.twig', []);
    }
}
