<?php

namespace App\Controller\admin;

use App\Entity\Figure;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("admin/picture")
 */
class PictureController extends AbstractController
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @Route("/new", name="admin.picture.new", methods={"GET","POST"})
	 */
	public function new(Request $request): Response
	{
		$picture = new Picture();
		$form = $this->createForm(PictureType::class, $picture);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$date = new \DateTime();
			$picture->setUpdatedAt($date);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($picture);
			$entityManager->flush();

			return $this->redirectToRoute('home');
		}

		return $this->render('admin/picture/new.html.twig', [
			'picture' => $picture,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}/{idFigure}/edit", name="admin.picture.edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Picture $picture): Response
	{
		$params = $request->attributes->get('_route_params');
		$idFigure = $params['idFigure'];
		$figure = $this->getDoctrine()
			->getRepository(Figure::class)
			->find($idFigure);

		$form = $this->createForm(PictureType::class, $picture);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$picture->setUpdatedAt(new \DateTime());
			$picture->setImageFile($form->get('imageFile')->getData());
			$this->em->flush();
			$this->addFlash('success', 'L\'image a bien été modifiée');
			return $this->redirectToRoute('home');
		}

		return $this->render('admin/picture/edit.html.twig', [
			'picture' => $picture,
			'figure' => $figure,
			'current_menu' => "admin.picture.edit",
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="admin.picture.delete", methods={"DELETE"})
	 */
	public function delete(Request $request, Picture $picture): Response
	{
		$data = json_decode($request->getContent(), true);

		if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($picture);
			$entityManager->flush();
			return new JsonResponse(['success' => 1]);
		}

		return new JsonResponse(['error' => 'Une erreur est survenue'], 400);
	}
}