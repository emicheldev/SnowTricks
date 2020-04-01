<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\ResetPasswordUserType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserValidate extends AbstractController
{
	/**
	 * @var ObjectManager
	 */
	private $manager;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	public function __construct(ObjectManager $manager, UserPasswordEncoderInterface $encoder)
	{
		$this->manager = $manager;
		$this->encoder = $encoder;
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
			$user->setToken(null);
			$user->setActif(1);
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
	public function resetPassword(Request $request, User $user): Response
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
				$user->setToken(null);
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