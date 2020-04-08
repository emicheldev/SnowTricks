<?php

namespace App\Controller\Security;

use App\Entity\User;
use Twig\Environment;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\ForgotPasswordUserType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

	/**
	 * @var UserRepository
	 */
	private $repository;

	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * Undocumented variable
	 *
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * Undocumented variable
	 *
	 * @var Environment
	 */
	private $renderer;


	public function __construct(UserRepository $repository, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, Environment $renderer)
	{
		$this->repository = $repository;
		$this->em = $em;
		$this->encoder = $encoder;
		$this->mailer = $mailer;
		$this->renderer = $renderer;
	}

	/**
	 * @Route("/login", name="login")
	 */
	public function login(AuthenticationUtils $authenticationUtils)
	{
		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

		//dd($authenticationUtils);


		return $this->render('security/login.html.twig', [
			'error' => $error,
			'current_menu' => 'login'
		]);
	}

	/**
	 * @Route("/register", name="register")
	 */
	public function register(Request $request)
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$token = bin2hex(random_bytes(32));

			$user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
			$user->setActif(0);
			$user->setToken($token);

			$this->em->persist($user);
			$this->em->flush();
			$this->addFlash('success', 'Votre inscription a été acceptée. Vous avez reçu un mail de confirmation à l\'adresse que vous nous avez indiqué.');

			// envoi mail
			$message = (new \Swift_Message('Bienvenue ' . $user->getUsername() . ' ! Veuillez confirmer votre adresse mail.'))
				->setFrom('no-reply@snow-tricks.com')
				->setTo($user->getEmail())
				->setBody($this->renderer->render('emails/confirmation.html.twig', [
					'user' => $user,
				]), 'text/html');

			$this->mailer->send($message);

			return $this->redirectToRoute('home');
		}

		return $this->render('security/register.html.twig', [
			'current_menu' => 'register',
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/forgot-password", name="forgot.password")
	 */
	public function forgotPassword(Request $request)
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
				$this->em->persist($userExist);
				$this->em->flush();

				// envoi mail
				$message = (new \Swift_Message('Réinitialisation de votre mot de passe'))
					->setFrom('no-reply@snow-tricks.com')
					->setTo($userExist->getEmail())
					->setBody($this->renderer->render('emails/reset-password.html.twig', [
						'user' => $userExist,
					]), 'text/html');

				$this->mailer->send($message);
			}

			$this->addFlash('success', 'Si votre email est bien associé à un compte, vous avez reçu un mail vous invitant à réinitialiser votre mot de passe');
			return $this->redirectToRoute('home');
		}

		return $this->render('security/forgot-password.html.twig', [
			'current_menu' => 'register',
			'form' => $form->createView(),
		]);
	}
}