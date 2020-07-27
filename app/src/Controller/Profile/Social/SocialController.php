<?php

declare(strict_types=1);

namespace App\Controller\Profile\Social;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialController extends AbstractController
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
     * @Route("/profile/social", name="profile.social")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->users->get($this->getUser()->getId());

        return $this->render('app/profile/social/index.html.twig', [
            'user' => $user,
        ]);
    }
}
