<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CategoryController
 * 
 * @Route("/admin/category")
 */
class CategoryController extends AbstractController
{
	/**
	 * index
	 *
	 * @param  mixed $categoryRepository
	 * @return Response
	 * 
	 * @Route("/", name="admin.category.index", methods={"GET"})
	 */
	public function index(CategoryRepository $categoryRepository): Response
	{
		return $this->render('admin/category/index.html.twig', [
			'categories' => $categoryRepository->findAll(),
			'current_menu' => "admin.category.index"
		]);
	}

	/**
	 * new
	 *
	 * @param  mixed $request
	 * @return Response
	 * @Route("/new", name="admin.category.new", methods={"GET","POST"})
	 */
	public function new(Request $request): Response
	{
		$category = new Category();
		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($category);
			$entityManager->flush();

			$this->addFlash('success', 'La catégorie a bien été créée');
			
			return $this->redirectToRoute('admin.category.index');
		}

		return $this->render('admin/category/new.html.twig', [
			'category' => $category,
			'form' => $form->createView(),
			'current_menu' => "admin.category.new"
		]);
	}

	/**
	 * edit
	 *
	 * @param  mixed $request
	 * @param  mixed $category
	 * @return Response
	 * 
	 * @Route("/{id}/edit", name="admin.category.edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Category $category): Response
	{
		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', 'La catégorie a bien été modifiée');
			return $this->redirectToRoute('admin.category.index');
		}

		return $this->render('admin/category/edit.html.twig', [
			'category' => $category,
			'form' => $form->createView(),
			'current_menu' => "admin.category.edit"
		]);
	}

	/**
	 * delete
	 *
	 * @param  mixed $request
	 * @param  mixed $category
	 * @return Response
	 * 
	 * @Route("/{id}", name="admin.category.delete", methods={"DELETE"})
	 */
	public function delete(Request $request, Category $category): Response
	{
		if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($category);
			$entityManager->flush();
			$this->addFlash('success', 'La catégorie a bien été supprimée');
		}

		return $this->redirectToRoute('admin.category.index');
	}
}
