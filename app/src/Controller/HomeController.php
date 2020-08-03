<?php

declare(strict_types=1);

namespace App\Controller;

use App\ReadModel\Post\PostFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
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
     * @return Response
     */
    public function index(): Response
    {
        $posts = $this->posts->getAllForMainPage();

        return $this->render('app/home.html.twig', [
            'posts' => $posts
        ]);
    }
}
