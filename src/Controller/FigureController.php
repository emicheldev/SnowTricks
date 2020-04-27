<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManager;
use App\Services\PicturesUploader;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FigureController extends AbstractController
{

   /**
	 * @var CommentRepository
	 */
	private $commentRepository;

	/**
	 * @var FigureRepository
	 */
	private $figureRepository;

	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	public function __construct(CommentRepository $commentRepository, FigureRepository $figureRepository, EntityManagerInterface $manager)
	{
		$this->commentRepository = $commentRepository;
		$this->figureRepository = $figureRepository;
		$this->manager = $manager;
	}

	/**
	 * @Route("/figure/{slug}-{id}", name="figure.show", requirements={"slug": "[a-z0-9\-]*"})
	 * @return Response
	 * @param figure $figure
	 */
	public function show(Figure $figure, string $slug, Request $request): Response
	{
		$user = $this->getUser();
		$comments = $this->commentRepository->findItems(1, $figure->getId());
		$nbGroups = round($this->commentRepository->countAll($figure->getId()) / 10);

		if ($user) {
			$comment = new Comment();
			$form = $this->createForm(CommentType::class, $comment);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$date = new \DateTime();
				$comment->setCreatedAt($date);
				$comment->setFigure($figure);
				$comment->setUser($user);

				$this->manager->persist($comment);
				$this->manager->flush();
				$this->addFlash('success', 'Le commentaire a bien été rajouté');

				return $this->redirectToRoute('figure.show', [
					'id' => $figure->getId(),
					'slug' => $figure->getSlug(),
					'nbGroups' => $nbGroups,
				], 301);
			}
		}


		if ($slug === $figure->getSlug()) {
			return $this->render('figure/show.html.twig', [
				'figure' => $figure,
				'id' => $figure->getId(),
				'slug' => $figure->getSlug(),
				'current_menu' => 'figure.show',
				'date_is_same' => $figure->dateIsSame(),
				'comments' => $comments,
				'form' => isset($form) && $form ? $form->createView() : false,
				'nbGroups' => $nbGroups,
			]);
		}

		return $this->render('figure/show.html.twig', [
			'id' => $figure->getId(),
			'slug' => $figure->getSlug(),
			'nbGroups' => $nbGroups,
		]);
	}





}
