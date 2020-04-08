<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
	/**
	 * @var FigureRepository
	 */
	private $figureRepository;

	public function __construct(FigureRepository $figureRepository)
	{
		$this->figureRepository = $figureRepository;
	}

	/**
	 * @Route("/", name="home")
	 *
	 * @return Response
	 */
	public function index(): Response
	{
		$figures = $this->figureRepository->findItems();
		$nbGroups = round($this->figureRepository->countAll() / 15);

		return $this->render('pages/index.html.twig', [
			'current_menu' => 'home',
			'figures' => $figures,
			'nbGroups' => $nbGroups
		]);
	}

	/**
	 * @Route("/index/{index}", name="home.index")
	 *
	 * @return Response
	 */
	public function ajaxLoadItems(Request $request)
	{
		$params = $request->attributes->get('_route_params');
		$index = (int) $params['index'];
		$nbGroups = round($this->figureRepository->countAll() / 15);

		if (is_int($index) && $index > 1) {
			$moreFigure = (array) $this->figureRepository->findMoreItems($index);
			$htmlData = [];

			if ($moreFigure) {
				foreach ($moreFigure as $figure) {
					$figure = $this->getDoctrine()
						->getRepository(Figure::class)
						->find($figure['id']);

					array_push(
						$htmlData,
						$this->renderView('./figure/_figure.html.twig', [
							'current_menu' => 'home',
							'figure' => $figure,
							'nbGroups' => $nbGroups,
						])
					);
				}
			}

			return new JsonResponse([
				'html' => $htmlData,
			], 200);
		}

		return new JsonResponse(['error' => 'Une erreur est survenue'], 400);
	}
}