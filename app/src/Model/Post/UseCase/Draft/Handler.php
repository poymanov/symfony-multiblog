<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Draft;

use App\Model\Flusher;
use App\Model\Post\Entity\Post\AuthorId;
use App\Model\Post\Entity\Post\Id;
use App\Model\Post\Entity\Post\Post;
use App\Model\Post\Entity\Post\PostRepository;
use App\Model\Post\Service\Slugger;
use DomainException;
use Exception;

class Handler
{
    private PostRepository $posts;

    private Flusher $flusher;

    /**
     * @param PostRepository $posts
     * @param Flusher        $flusher
     */
    public function __construct(PostRepository $posts, Flusher $flusher)
    {
        $this->posts   = $posts;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     *
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $post = $this->posts->get(new Id($command->id));
        $post->draft();

        $this->flusher->flush();
    }
}
