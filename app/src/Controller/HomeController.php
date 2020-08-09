<?php

declare(strict_types=1);

namespace App\Controller;

use App\ReadModel\Post\PostFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private const POSTS_PER_PAGE = 20;

    private PostFetcher $posts;

    /**
     * @param PostFetcher $posts
     */
    public function __construct(PostFetcher $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $pagination = $this->posts->getAllForMainPage(
            $request->query->getInt('page', 1),
            self::POSTS_PER_PAGE
        );

        return $this->render('app/home.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
