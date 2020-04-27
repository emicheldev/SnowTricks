<?php

namespace App\Controller\admin;

use App\Entity\Figure;
use App\Entity\Picture;
use App\Form\FigureType;
use App\Form\PictureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Laminas\EventManager\EventManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * FigureController
 */
class FigureController extends AbstractController
{

	/**
	 * @var FiguresRepository
	 */
	private $repository;
	/**
	 * @var EntityManagerInterface
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $manager;

	public function __construct(FigureRepository $repository, EntityManagerInterface $manager)
	{
		$this->repository = $repository;
		$this->manager = $manager;
	}

	/**
	 * @Route("/admin/figure/create", name="admin.figure.new")
	 * new
	 *
	 * @param  mixed $request
	 * @return void
	 */
	public function new(Request $request)
	{
		$figure = new Figure();
		$form = $this->createForm(FigureType::class, $figure);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$date = new \DateTime();
			$figure->setCreatedAt($date);
			$figure->setUpdatedAt($date);

			$this->manager->persist($figure);
			$this->manager->flush();
			$this->addFlash('success', 'La figure a bien été créée');
			return $this->redirectToRoute('home');
		}

		$lastId = $this->repository->getLastId()->getId();

		return $this->render('admin/figure/new.html.twig', [
			'figure' => $figure,
			'idFigure' => $lastId + 1,
			'form'     => $form->createView(),
			'current_menu' => 'admin.figure.new',
		]);
	}

	/**
	 * @Route("/admin/figure/{id}", name="admin.figure.edit", methods="GET|POST")
	 * @param Figure $figure
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * 
	 * edit
	 *
	 * @param  mixed $figure
	 * @param  mixed $request
	 * @return void
	 */
	public function edit(Figure $figure, Request $request)
	{
		$form = $this->createForm(FigureType::class, $figure);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$date = new \DateTime();
			$figure->setUpdatedAt($date);

			$this->manager->flush();
			$this->addFlash('success', 'La figure a bien été modifiée');
			return $this->redirectToRoute('home');
		}

		return $this->render('admin/figure/edit.html.twig', [
			'figure' => $figure,
			'form'     => $form->createView(),
			'current_menu' => 'admin.figure.edit',
		]);
	}

	/**
	 * @Route("/admin/figure/{id}", name="admin.figure.delete", methods="DELETE")
	 * @param Figure $figure
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * 
	 * delete
	 *
	 * @param  mixed $figure
	 * @param  mixed $request
	 * @return void
	 */
	public function delete(Figure $figure, Request $request)
	{
		if ($this->isCsrfTokenValid('delete' . $figure->getId(), $request->get('_token'))) {
			$this->manager->remove($figure);
			$this->manager->flush();
			$this->addFlash('success', 'La figure a bien été supprimée');
		} else {
			$this->addFlash('error', 'La figure n\'a pas été supprimée, un problème est survenu');
			return $this->redirectToRoute('home');
		}
		return $this->redirectToRoute('home');
	}

	/**
	 * @Route("/admin/figure/mainImg/{id}", name="admin.figure.mainImg.delete", methods="DELETE")
	 * @param Figure $figure
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * deleteMainImg
	 *
	 * @param  mixed $figure
	 * @param  mixed $request
	 * @return void
	 */
	public function deleteMainImg(Figure $figure, Request $request)
	{
		if ($this->isCsrfTokenValid('delete' . $figure->getId(), $request->get('_token'))) {
			$figure->setMainImage(null);
			$figure->setUpdatedAt(new \DateTime());
			$this->manager->flush();
			$this->addFlash('success', 'L\'image principale de la figure a bien été supprimée');
		} else {
			$this->addFlash('error', 'La figure n\'a pas été supprimée, un problème est survenu');
			return $this->redirectToRoute('home');
		}
		return $this->redirectToRoute('home');
	}
}