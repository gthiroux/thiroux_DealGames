<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function PHPUnit\Framework\throwException;

#[Route('/user')]
final class UserController extends AbstractController
{

    // * Check if the user is connected and get the role ADMIN or USER
    // * if the user isn't connected, redirect to the login's route
    #[Route(name: 'app_user_index', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
    public function index(#[CurrentUser] User $user,): Response
    {
        $ads=$user ->getAds();
        return $this->render('user/show.html.twig',['user'=>$user,
        'ads'=>$ads]);
    }
        
    // * if the usehas a ADMIN's role , he can see all user in database
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN"))'))]
    #[Route('/all',name:"app_user_all",methods:['GET'])]
    public function findAllUser(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            ]);
    }

    // * if the user has a ADMIN's role, he can create a new user 
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN"))'))]
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
            
            return $this->render('user/new.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
    }
    
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN"))'))]
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            ]);
    }
            
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $user != $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier le profil d'un autre utilisateur.");
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
    
            if (!empty($plainPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }
            $entityManager->flush();
            
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_USER")'))]
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $user !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer ce compte.");
        }
        foreach ($user->getAds() as $ad) {
            $entityManager->remove($ad);
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        if ($user === $this->getUser()) {
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);
            return $this->redirectToRoute('app_ad_index'); 
         }                     
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
