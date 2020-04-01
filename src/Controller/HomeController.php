<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController  extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TrickRepository $rep)
    {
        $allTricks = $rep->findBy([], ['createdAt' => 'DESC'], 8, 0);

       return $this->render('pages/index.html.twig', ['allTricks' => $allTricks]);
    }
}
