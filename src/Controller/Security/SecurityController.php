<?php

namespace App\Controller\Security;

use App\Entity\User;
use Twig\Environment;
use App\Form\UserType;
use App\Services\SendMail;
use App\Repository\UserRepository;
use App\Form\ForgotPasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
	 * @var MailerInterface 
	 */
	private $mailer;


	/**
	 * Undocumented variable
	 *
	 * @var Environment
	 */
	private $renderer;


	public function __construct(UserRepository $repository, EntityManagerInterface $em, MailerInterface $mailer, UserPasswordEncoderInterface $encoder, Environment $renderer)
	{
		$this->mailer = $mailer;
		$this->repository = $repository;
		$this->em = $em;
		$this->encoder = $encoder;
		$this->renderer = $renderer;
	}

	/**
	 * @Route("/login", name="login")
	 */
	public function login(AuthenticationUtils $authenticationUtils)
	{
		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

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
			$SendMail= new SendMail($this->mailer, $user->getEmail(), 'Merci pour votre inscription', 'emails/confirmation.html.twig',$user);

			$SendMail->sendNotification();
		

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
				$SendMail= new SendMail($this->mailer, $userExist->getEmail(), 'Réinitialisation de votre mot de passe', 'emails/reset-password.html.twig', $userExist);

				$SendMail->sendNotification();

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