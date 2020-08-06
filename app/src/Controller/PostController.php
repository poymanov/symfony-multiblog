<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Post\Entity\Post\Post;
use App\ReadModel\User\UserFetcher;
use App\Model\Like\UseCase\Like\Create;
use App\Model\Like\UseCase\Like\Delete;
use App\Service\Like;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private UserFetcher $users;

    private ErrorHandler $errors;

    /**
     * @param UserFetcher  $users
     * @param ErrorHandler $errors
     */
    public function __construct(UserFetcher $users, ErrorHandler $errors)
    {
        $this->users  = $users;
        $this->errors = $errors;
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

    /**
     * @Route("/posts/{alias}/like", name="post.like", methods={"post"})
     * @param Post           $post
     *
     * @param Create\Handler $handler
     *
     * @return Response
     * @throws \Exception
     */
    public function like(Post $post, Create\Handler $handler): Response
    {
        $like = new Create\Command(
            $this->getUser()->getId(),
            Post::class,
            $post->getId()->getValue()
        );

        try {
            $handler->handle($like);
            $this->addFlash('success', 'Публикация отмечена как понравившаяся.');
            return $this->redirectToRoute('post', ['alias' => $post->getAlias()]);
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/posts/{alias}/delete-like", name="post.like.delete", methods={"delete"})
     * @param Post           $post
     *
     * @param Delete\Handler $handler
     *
     * @return Response
     * @throws \Exception
     */
    public function deleteLike(Post $post, Delete\Handler $handler): Response
    {
        $like = new Delete\Command(
            $this->getUser()->getId(),
            Post::class,
            $post->getId()->getValue()
        );

        try {
            $handler->handle($like);
            $this->addFlash('success', 'Публикация удалена из списка понравившихся.');
            return $this->redirectToRoute('post', ['alias' => $post->getAlias()]);
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('home');
        }
    }
}
