<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignUpController extends AbstractController
{
    /**
     * @Route("/signup", name="auth.signup")
     * @param Request $request
     * @return Response
     */
    public function request(Request $request): Response
    {
        return $this->render('app/auth/signup.html.twig');
    }
}
