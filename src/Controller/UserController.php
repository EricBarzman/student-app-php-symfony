<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Utils\Utils;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    public function __construct(private Utils $utils) {}

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
        ): Response
    {
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pincode = $this->utils->generateRandomPincode();
            
            // Check if pincode already exists, generate a new one in that case
            while(!is_null($userRepository->findOneByPincode($pincode))) {
                $pincode = $this->utils->generateRandomPincode();
            };
            $user->setPincode($pincode);
            $user->setPassword($userPasswordHasher->hashPassword($user, $pincode));
            
            $roles = $request->request->get('roles');
            $user->setRoles([$roles]);

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_show', array('id' => $user->getId()));
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // Retrieve roles (as it's treated outside the form)
            $roles = $request->request->get('roles');
            $user->setRoles([$roles]);

            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/edit/newpin/{id}', name: 'app_user_change_pincode')]
    public function change_pincode(string $id, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $pincode = $this->utils->generateRandomPincode(); 
        // Check if pincode already exists, generate a new one in that case
        while(!is_null($userRepository->findOneByPincode($pincode))) $pincode = $this->utils->generateRandomPincode();
        
        $user->setPincode($pincode);
        $user->setPassword($userPasswordHasher->hashPassword($user, $pincode));    
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_show', ['id' => $id]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
