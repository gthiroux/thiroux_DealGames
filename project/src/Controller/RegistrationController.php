<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\JWTService;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        JWTService $jwt,
        SendEmailService $mail
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Générer le token JWT
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = ['user_id' => $user->getId()];
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwt_secret'));

            // Envoyer l'e-mail de confirmation
            $mail->send(
                'no-reply@openblog.test',
                $user->getEmail(),
                'Activation de votre compte sur le site OpenBlog',
                'register',
                compact('user', 'token')
            );

            $this->addFlash('success', 'Inscription réussie ! Vérifiez votre email pour activer votre compte.');

            return $this->redirectToRoute('app_login'); 
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifUser($token, JWTService $jwt, UserRepository $usersRepository, EntityManagerInterface $em): Response
    {
        // On vérifie si le token est valide (cohérent, pas expiré et signature correcte)
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwt_secret'))) {
            // Le token est valide
            // On récupère les données (payload)
            $payload = $jwt->getPayload($token);

            // On récupère le user
            $user = $usersRepository->find($payload['user_id']);

            // On vérifie qu'on a bien un user et qu'il n'est pas déjà activé
            if ($user && !$user->isVerified()) {
                $user->setIsVerified(true);
                $em->flush();

                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('home');
            }
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('home');
    }

}