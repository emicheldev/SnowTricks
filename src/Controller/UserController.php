<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
    * @Route("/register", name="user_register")
    */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = bin2hex(random_bytes(32));

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setImagePath('/img/default/default.png');
            $user->setImageName('default.png');
            $user->setToken($token);
            $user->setActivated(0);
            $user->getRoles();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);

            $entityManager->flush();

            // send mail
			$message = (new \Swift_Message('Bienvenue ' . $user->getUsername() . ' ! Veuillez confirmer votre adresse mail.'))
            ->setFrom('no-reply@snow-tricks.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderer->render('emails/confirmation.html.twig', [
                'user' => $user,
            ]), 'text/html');

            $this->mailer->send($message);


            return $this->redirectToRoute('home');
        }

        return $this->render('/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
