<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Model\User\Entity\User\User;
use App\ReadModel\Post\PostFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private const POSTS_PER_PAGE = 5;

    private PostFetcher $posts;

    /**
     * @param PostFetcher $posts
     */
    public function __construct(PostFetcher $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @Route("/users/{alias}", name="users.profile")
     *
     * @param User $user
     *
     * @return Response
     */
    public function show(User $user): Response
    {
        if ($user->isWait()) {
            throw new NotFoundHttpException();
        }

        $userId = $user->getId()->getValue();

        $posts = $this->posts->allPublishedForUser($user->getId()->getValue(), 1, self::POSTS_PER_PAGE);

        return $this->render('app/users/profile.html.twig', [
            'user'     => $user,
            'likes'    => $this->posts->countLikesForUserPosts($userId),
            'comments' => $this->posts->countCommentsForUserPosts($userId),
            'posts'    => $posts,
        ]);
    }
}
