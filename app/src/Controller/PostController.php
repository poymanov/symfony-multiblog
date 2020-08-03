<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Post\Entity\Post\Post;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
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
     * @Route("/posts/{alias}", name="post")
     * @param Post $post
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function show(Post $post): Response
    {
        if ($post->getStatus()->isDraft()) {
            throw new NotFoundHttpException();
        }

        $author = $this->users->get($post->getAuthorId()->getValue());

        return $this->render('app/post.html.twig', [
            'post' => $post,
            'author' => $author,
        ]);
    }
}
