<?php

namespace App\Controller;

use App\Entity\User;
use Twig\Environment;
use App\Form\UserType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Form\ResetPasswordUserType;
use App\Form\ForgotPasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
	 * @var ObjectManager
	 */
	private $manager;

    /**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * Undocumented variable
	 *
	 * @var MailerInterface 
	 */
	private $mail;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, Environment $renderer)
	{
        $this->repository = $repository;
        $this->manager = $manager;
		$this->encoder = $encoder;
	}

    /**
     * @Route("/login", name="auth_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Security $username
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Security $username)
    {

        if ($username->getUser() === null ) {

            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        }

        return $this->redirectToRoute('home');
    }

    /**
	 * @Route("/register", name="register")
	 */
	public function register(Request $request, MailerInterface $mailer)
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$token = bin2hex(random_bytes(32));

			$user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $user->setToken($token);
			$user->setActivated(0);
            $user->setImagePath('/img/default/default.png');
            $user->setImageName('default.png');

			$this->manager->persist($user);
			$this->manager->flush();
			$this->addFlash('success', 'Votre inscription a été acceptée. Vous avez reçu un mail de confirmation à l\'adresse que vous nous avez indiqué.');

            // envoi mail
			$message = (new TemplatedEmail())
				->from('no-reply@snow-tricks.com')
                ->to($user->getEmail())
                ->subject('Thanks for signing up!')
                ->htmlTemplate('emails/confirmation.html.twig')
                ->context([
                    'user' => $user,
                ]);

            $mailer->send($message);
            
			return $this->redirectToRoute('home');
		}

		return $this->render('security/register.html.twig', [
			'current_menu' => 'register',
			'form' => $form->createView(),
		]);
    }
    

    /**
	 * @Route("/forgot-password", name="forgot_password")
	 */
	public function forgotPassword(Request $request, MailerInterface $mailer)
	{
		$user = new User();
		$form = $this->createForm(ForgotPasswordUserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$token = bin2hex(random_bytes(32));
			$email = $user->getEmail();

			$userExist = $this->getDoctrine()
				->getRepository(User::class)
				->findBy(array('email' => $email));

			$userExist = isset($userExist[0]) ? $userExist[0] : false;

			if ($userExist) {
				$userExist->setToken($token);
				$this->manager->persist($userExist);
                $this->manager->flush();
                
                // envoi mail
                $message = (new TemplatedEmail())
                ->from('no-reply@snow-tricks.com')
                ->to($userExist->getEmail())
                ->subject('Thanks for signing up!')
                ->htmlTemplate('emails/reset-password.html.twig')
                ->context([
                    'user' => $userExist,
                ]);

                 $mailer->send($message);
			}

			$this->addFlash('success', 'Si votre email est bien associé à un compte, vous avez reçu un mail vous invitant à réinitialiser votre mot de passe');
			return $this->redirectToRoute('home');
		}

		return $this->render('security/forgot-password.html.twig', [
			'current_menu' => 'register',
			'form' => $form->createView(),
		]);
	}


    /**
     * @Route("/logout", name="auth_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
	 * @Route("/validate-user/{idUser}/{token}", name="security.validate.user")
	 */
	public function validateUser(Request $request, User $user)
	{
		$params = $request->attributes->get('_route_params');
		$id = $params['idUser'];
		$token = $params['token'];
		$user = $this->getDoctrine()
			->getRepository(User::class)
			->find($id);

		if ($user->getToken() === $token) {
			$user->setToken(0);
			$user->setActivated(1);
			$this->manager->persist($user);
			$this->manager->flush();
			$this->addFlash('success', 'Inscription confirmée');

			return $this->redirectToRoute('home');
		}

		$this->addFlash('error', 'Inscription non confirmée, un problème est survenu');
		return $this->redirectToRoute('home');
    }
    
    /**
	 * @Route("/reset-password/{id}/{token}", name="security.reset.user")
	 */
	public function resetPassword(Request $request, User $user)
	{
		$params = $request->attributes->get('_route_params');
		$id = $params['id'];
		$token = $params['token'];
		$user = $this->getDoctrine()
			->getRepository(User::class)
			->find($id);

		if ($user->getToken() === $token) {
			$form = $this->createForm(ResetPasswordUserType::class, $user);
            $form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
				$user->setToken(0);
				$this->manager->persist($user);
				$this->manager->flush();

				$this->addFlash('success', 'Votre mot de passe a bien été réinitialisé');
				return $this->redirectToRoute('home');
			}

			return $this->render('security/reset-password.html.twig', [
				'current_menu' => 'register',
				'form' => $form->createView(),
			]);
		}

		$this->addFlash('error', 'Le lien de réinitialisation du mot de passe a expiré, veuillez recommencer');
		return $this->redirectToRoute('home');
	}

}