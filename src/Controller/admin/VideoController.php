<?php

namespace App\Controller\admin;

use App\Entity\Video;
use App\Entity\Figure;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/video")
 */
class VideoController extends AbstractController
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
	 * @Route("/{idFigure}/new", name="admin.video.new", methods={"GET","POST"})
	 */
	public function new(Request $request): Response
	{
		$params = $request->attributes->get('_route_params');
		$idFigure = $params['idFigure'];
		$figure = $this->getDoctrine()
			->getRepository(Figure::class)
			->find($idFigure);

		$video = new Video();
		$form = $this->createForm(VideoType::class, $video);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$video->setFigure($figure);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($video);
			$entityManager->flush();

			$this->addFlash('success', 'La vidéo a bien été ajoutée');
			return $this->redirectToRoute('home');
		}

		return $this->render('admin/video/new.html.twig', [
			'video' => $video,
			'form' => $form->createView(),
			'current_menu' => 'admin.video.new',
			'idFigure' => $idFigure
		]);
	}

	/**
	 * @Route("/{id}/edit", name="admin.video.edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Video $video): Response
	{
		$idFigure = $video->getFigure()->getId();

		$form = $this->createForm(VideoType::class, $video);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', 'La vidéo a bien été modifiée');
			return $this->redirectToRoute('home');
		}

		return $this->render('admin/video/edit.html.twig', [
			'video' => $video,
			'form' => $form->createView(),
			'current_menu' => 'admin.video.edit',
			'idFigure' => $idFigure
		]);
	}

	/**
	 * @Route("/{id}", name="admin.video.delete",  methods="DELETE")
	 * @param Video $video
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function delete(Request $request, Video $video): Response
	{
		$routeParams = $request->query->get('idFigure');

		if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->get('_token'))) {
			$this->em->remove($video);
			$this->em->flush();
			$this->addFlash('success', 'La vidéo a bien été supprimée');
		} else {
			$this->addFlash('error', 'La vidéo n\'a pas été supprimée, un problème est survenu');
			return $this->redirectToRoute('admin.figure.edit', [
			'idFigure' => $routeParams
		]);
		}

		return $this->redirectToRoute('admin.figure.edit',[
			'id' => $routeParams
		]);
	}
}