<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\sendMailService;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(UsersAuthenticator $authenticator,UserAuthenticatorInterface $userAuthenticator,JWTService $jwt ,sendMailService $mail ,Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->addFlash('warning','votre compote nest pas activee veuillez verifier votre email');

           
             $entityManager->persist($user);
             $entityManager->flush();

            //on genere le jwt de l'utilisateur
            //on fait le header 
            $header = ['typ'=>'JWT','alg'=>'HS256'];
            $payload= ['user_id'=>$user->getId()];

            //on genere le token 
            $token = $jwt->generate($header,$payload, $this->getParameter('app.jwtsecret'));
            
            // do anything else you need here, like send an email
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur le site e-commerce',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser(EntityManagerInterface $em ,$token , JWTService $jwt , UsersRepository $usersRepository): Response
    {
        //on verifie que le token n'a pas expire et n'a pas ete modifié
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            // on nrecupére le payload
            $payload = $jwt->getPayload($token);

            //on recupere le user du token 
            $user = $usersRepository->find($payload['user_id']);

            //on verifie que l'utilisateur existe et n'a pas encore activer son compte 
            if($user && !$user->isIsVerified()){
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success','votre compte est activé');
                return $this->redirectToRoute('profile_index');
            
            }

        }
        $this->addFlash('danger','le token est invalide ou a expiré');
        return $this->redirectToRoute('app_register');
    }

    #[Route('/renvoieverif', name:'resend_verif')]
    public function resendVerif(JWTService $jwt , sendMailService $mail , UsersRepository $usersRepository) :Response
    {
        $user =  $this->getUser();
        if(!$user)
        {
         $this->addFlash('warning','cette utilisateur est déja activé');
         return $this->redirectToRoute('app_login');
        }
         //on genere le jwt de l'utilisateur
            //on fait le header 
            $header = ['typ'=>'JWT','alg'=>'HS256'];
            $payload= ['user_id'=>$user->getId()];

            //on genere le token 
            $token = $jwt->generate($header,$payload, $this->getParameter('app.jwtsecret'));
            
            // do anything else you need here, like send an email
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur le site e-commerce',
                'register',
                compact('user', 'token')
            );
            $this->addFlash('success','Email de verification envoyé');
            return $this->redirectToRoute('app_login');
    }


    }