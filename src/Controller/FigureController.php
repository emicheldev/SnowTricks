<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManager;
use App\Services\PicturesUploader;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FigureController extends AbstractController
{

   /**
	 * @var FigureRepository
	 */
	private $repository;
	/**
	 * @var EntityManagerInterface
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $manager;

	public function __construct( EntityManagerInterface $manager)
	{
		$this->manager = $manager;
	}
    




}
