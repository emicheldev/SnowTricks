<?php

namespace App\Controller;

use App\Domain\Auth\User;
use App\Domain\Password\Data\PasswordResetConfirmData;
use App\Domain\Password\Data\PasswordResetRequestData;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Form\PasswordResetConfirmForm;
use App\Domain\Password\Form\PasswordResetRequestForm;
use App\Domain\Password\PasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{

    /**
     * @Route("/password/new", name="auth_password_reset")
     */
    public function reset(Request $request): Response
    {
        
        return $this->render('auth/password_reset.html.twig');
    }

}