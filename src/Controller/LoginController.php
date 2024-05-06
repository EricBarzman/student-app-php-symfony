<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, RateLimiterFactory $_login_local_mainLimiter, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) return $this->redirectToRoute('app_home');
        
        // Retrieve number of attempts and remaining time
        $limiter = $_login_local_mainLimiter->create($request->getClientIp());
        $limitToken = $limiter->consume(0)->getRemainingTokens();
        $now = new DateTime('now');
        $limitTime = date_diff($now, $limiter->consume(0)->getRetryAfter());
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        return $this->render('security/login.html.twig', [
            'limitToken' => $limitToken,
            'limitTime' => $limitTime
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
