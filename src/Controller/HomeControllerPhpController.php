<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeControllerPhpController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
       return $this->render('pages/index.html.twig');
    }
}
