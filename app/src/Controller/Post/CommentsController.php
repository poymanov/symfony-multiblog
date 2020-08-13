<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\ErrorHandler;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\UseCase\Comment as CommentUseCase;
use App\Model\Post\Entity\Post\Post;
use App\ReadModel\Comment\CommentFetcher;
use App\ReadModel\Post\PostFetcher;
use App\Security\Voter\Post\CommentAccess;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class CommentsController extends AbstractController
{
    private const COMMENTS_PER_PAGE = 20;

    private ErrorHandler $errors;

    private CommentFetcher $comments;

    private PostFetcher $posts;

    /**
     * @param ErrorHandler   $errors
     * @param CommentFetcher $comments
     * @param PostFetcher    $posts
     */
    public function __construct(ErrorHandler $errors, CommentFetcher $comments, PostFetcher $posts)
    {
        $this->errors   = $errors;
        $this->comments = $comments;
        $this->posts    = $posts;
    }

    /**
     * @Route("/posts/{alias}/comments", name="post.comments")
     * @param Post $post
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function show(Post $post, Request $request): Response
    {
        if ($post->getStatus()->isDraft()) {
            throw new NotFoundHttpException();
        }

        $pagination = $this->comments->allForEntity(
            new Entity(Post::class, $post->getId()->getValue()),
            $request->query->getInt('page', 1),
            self::COMMENTS_PER_PAGE
        );

        return $this->render('app/posts/comments/comments.html.twig', [
            'post'       => $post,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/posts/{alias}/comments/create", name="post.comments.create")
     * @param Post                          $post
     *
     * @param Request                       $request
     *
     * @param CommentUseCase\Create\Handler $handler
     *
     * @return Response
     * @throws \Exception
     */
    public function create(Post $post, Request $request, CommentUseCase\Create\Handler $handler): Response
    {
        if ($post->getStatus()->isDraft()) {
            throw new NotFoundHttpException();
        }

        $command = new CommentUseCase\Create\Command(
            $this->getUser()->getId(),
            Post::class,
            $post->getId()->getValue()
        );

        $form = $this->createForm(CommentUseCase\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Комментарий добавлен.');
                return $this->redirectToRoute('post.comments', ['alias' => $post->getAlias()]);
            } catch (DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/posts/comments/create.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/comments/{id}/edit", name="post.comments.edit", requirements={"id"=App\Annotation\Guid::PATTERN})
     * @param Comment                     $comment
     * @param Request                     $request
     *
     * @param CommentUseCase\Edit\Handler $handler
     *
     * @return Response
     * @throws \Exception
     */
    public function edit(Comment $comment, Request $request, CommentUseCase\Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(CommentAccess::EDIT, $comment);

        try {
            $post = $this->posts->get($comment->getEntity()->getId());
        } catch (Throwable $e) {
            throw new NotFoundHttpException();
        }

        if ($post->getStatus()->isDraft()) {
            throw new NotFoundHttpException();
        }

        if ($comment->getEntity()->getId() != $post->getId()->getValue()) {
            throw new NotFoundHttpException();
        }

        if (!$comment->canEdit()) {
            throw new AccessDeniedHttpException();
        }

        $command = CommentUseCase\Edit\Command::fromComment($comment);

        $form = $this->createForm(CommentUseCase\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Комментарий изменен.');
                return $this->redirectToRoute('post.comments', ['alias' => $post->getAlias()]);
            } catch (DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/posts/comments/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/comments/{id}/delete", name="post.comments.delete", requirements={"id"=App\Annotation\Guid::PATTERN}, methods={"DELETE"})
     *
     * @param Comment $comment
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Comment $comment, Request $request, CommentUseCase\Remove\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(CommentAccess::DELETE, $comment);

        try {
            $post = $this->posts->get($comment->getEntity()->getId());
        } catch (Throwable $e) {
            throw new NotFoundHttpException();
        }

        if ($post->getStatus()->isDraft()) {
            throw new NotFoundHttpException();
        }

        $command = new CommentUseCase\Remove\Command($comment->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Комментарий удален.');
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('post.comments', ['alias' => $post->getAlias()]);
    }
}
