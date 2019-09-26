<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(EntityManagerInterface $entityManager,Request $request,UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form=$this->createForm(RegistrationType::Class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash= $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success','Vous avez été enregistré avec succés');
                return $this->redirectToRoute('security_login');
            }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

/**
 * @Route("/connexion", name="security_login")
 */
    public function login(AuthenticationUtils $authenticationUtils){
        
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

/**
 * @Route("/deconnexion", name="security_logout")
 */
public function logout(){}
    
}
