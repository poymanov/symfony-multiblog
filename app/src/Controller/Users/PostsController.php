<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Model\User\Entity\User\User;
use App\ReadModel\Post\PostFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    private const POSTS_PER_PAGE = 20;

    /**
     * @var PostFetcher
     */
    private PostFetcher $posts;

    /**
     * @param PostFetcher $posts
     */
    public function __construct(PostFetcher $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @Route("/users/{alias}/posts", name="users.posts")
     *
     * @param User    $user
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(User $user, Request $request): Response
    {
        if ($user->isWait()) {
            throw new NotFoundHttpException();
        }

        $posts = $this->posts->allPublishedForUser(
            $user->getId()->getValue(),
            $request->query->getInt('page', 1),
            self::POSTS_PER_PAGE
        );

        return $this->render('app/users/posts.html.twig', [
            'user'  => $user,
            'posts' => $posts,
        ]);
    }

}
