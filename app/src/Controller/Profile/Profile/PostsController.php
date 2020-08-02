<?php

declare(strict_types=1);

namespace App\Controller\Profile\Profile;

use App\Controller\ErrorHandler;
use App\Model\Post\Entity\Post\Post;
use App\Model\Post\UseCase\Create;
use App\Model\Post\UseCase\Draft;
use App\Model\Post\UseCase\Edit;
use App\Model\Post\UseCase\Publish;
use App\ReadModel\Post\PostFetcher;
use App\Security\Voter\Post\ProfilePostAccess;
use DomainException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/profile/posts", name="profile.posts")
 */
class PostsController extends AbstractController
{
    private ErrorHandler $errors;

    private PostFetcher $posts;

    /**
     * @param ErrorHandler $errors
     * @param PostFetcher  $posts
     */
    public function __construct(ErrorHandler $errors, PostFetcher $posts)
    {
        $this->errors = $errors;
        $this->posts  = $posts;
    }

    /**
     * @Route("", name="")
     * @return Response
     */
    public function index(): Response
    {
        $posts = $this->posts->allForUser($this->getUser()->getId());

        return $this->render('app/profile/posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/create", name=".create")
     * @param Request        $request
     * @param Create\Handler $handler
     *
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command($this->getUser()->getId());

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Новая запись опубликована.');
                return $this->redirectToRoute('profile.posts');
            } catch (DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/profile/posts/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name=".edit")
     * @param Post         $post
     * @param Request      $request
     *
     * @param Edit\Handler $handler
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Post $post, Request $request, Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProfilePostAccess::MANAGE, $post);

        $command = Edit\Command::fromPost($post);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Публикация изменена.');
                return $this->redirectToRoute('profile.posts');
            } catch (DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/profile/posts/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    /**
     * @Route("/publish/{id}", name=".publish", methods={"patch"})
     * @param Post            $post
     * @param Request         $request
     *
     * @param Publish\Handler $handler
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function publish(Post $post, Request $request, Publish\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProfilePostAccess::MANAGE, $post);

        $command = new Publish\Command($post->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Публикация опубликована.');
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('profile.posts');
    }

    /**
     * @Route("/draft/{id}", name=".draft", methods={"patch"})
     *
     * @param Post          $post
     * @param Request       $request
     *
     * @param Draft\Handler $handler
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function draft(Post $post, Request $request, Draft\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProfilePostAccess::MANAGE, $post);

        $command = new Draft\Command($post->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Публикация переведена в черновики.');
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('profile.posts');
    }
}
