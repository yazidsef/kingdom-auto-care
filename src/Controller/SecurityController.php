<?php

namespace App\Controller;

//ce fichier est avec authentification et deconnexion

use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UsersRepository;
use App\Service\sendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //cette methode verifie si l'utilisateur est connecté et si il
        //est connecter il le redirige vers la page d'accueil
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one comme erreur de mot de passe ou d'email errorné
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user comme email ou username
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    //Route et methode de mot de passe oublier
    #[Route('/oubli-pass' , name:'forgotten_passowrd')]
    public function forgottenPassowrd(EntityManagerInterface $em,sendMailService $mail,TokenGeneratorInterface $tokenGeneratorInterface,Request $request , UsersRepository $usersRepository): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //on vas chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());
            if($user){
                //on génére un token de renitialisation
            $token = $tokenGeneratorInterface->generateToken();
            $user->setResetToken($token);
            $em->persist($user);
            $em->flush();
            //on genere un lien de renitialisation de mot de passe

            $url = $this->generateUrl('reset_pass', ['token'=>$token],UrlGeneratorInterface::ABSOLUTE_URL);
            //on crée les données de l'email
            $context = compact('url','user');
            $mail->send(
                'noreply@e-commerce.com',
                $user->getEmail(),
                'Renitialisation de mot de passe',
                'password_reset',
                $context
            );
            $this->addFlash('success','Email envoyé avec succés');
            return $this->redirectToRoute('app_login');
        }
            
               $this->addFlash('danger','un probmléme est survenu');
               return $this->redirectToRoute('app_login');
          

        }
        return $this->render('security/reset_password_request.html.twig',['form'=>$form->createView()]);
    }

    #[Route('/oubli-pass/{token}' , name:'reset_pass')]
    public function resetPass(string $token , Request $request , UsersRepository $usersRepository , EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordHasher): Response
    {
        // on verifie si on a ce token dans la base de données 
        $user = $usersRepository ->findOneByResetToken($token);
        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) { 
                //onv efface le token 
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $form->get('password')->getData())
                );
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success','Mot de passe changer avec succes');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('security/reset_password.html.twig', compact('form'));
        }
        $this->addFlash('danger','jeton invalide ');
        return $this->redirectToRoute('app_login');


    }


}
