<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Create;

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
    private Slugger $slugger;

    private PostRepository $posts;

    private Flusher $flusher;

    /**
     * @param Slugger        $slugger
     * @param PostRepository $posts
     * @param Flusher        $flusher
     */
    public function __construct(Slugger $slugger, PostRepository $posts, Flusher $flusher)
    {
        $this->slugger = $slugger;
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
        $alias = $this->slugger->create($command->title);

        if ($this->posts->hasByAlias($alias)) {
            throw new DomainException('Публикация с таким alias уже существует.');
        }

        $post = new Post(
            Id::next(),
            new AuthorId($command->author),
            $alias,
            $command->title,
            $command->previewText,
            $command->text,
        );

        $this->posts->add($post);
        $this->flusher->flush();
    }
}
