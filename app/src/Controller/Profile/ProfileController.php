<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private UserFetcher $users;

    /**
     * @param UserFetcher $users
     */
    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('app/profile/profile.html.twig', [
            'user' => $this->users->findForAuthByEmail($this->getUser()->getUsername())
        ]);
    }
}
