<?php

declare(strict_types=1);

namespace App\Model\Like\UseCase\Like\Create;

use App\Model\Flusher;
use App\Model\Like\Entity\Like\AuthorId;
use App\Model\Like\Entity\Like\Entity;
use App\Model\Like\Entity\Like\Id;
use App\Model\Like\Entity\Like\Like;
use App\Model\Like\Entity\Like\LikeRepository;
use App\Model\Post\Entity\Post\PostRepository;
use App\Model\Post\Entity\Post\Id as PostId;
use DomainException;
use Exception;

class Handler
{
    private LikeRepository $likes;

    private Flusher $flusher;

    private PostRepository $posts;

    /**
     * @param LikeRepository $likes
     * @param Flusher        $flusher
     * @param PostRepository $posts
     */
    public function __construct(LikeRepository $likes, Flusher $flusher, PostRepository $posts)
    {
        $this->likes   = $likes;
        $this->flusher = $flusher;
        $this->posts   = $posts;
    }

    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $post = $this->posts->get(new PostId($command->entityId));

        if ($post->getAuthorId()->getValue() == $command->author) {
            throw new DomainException('Нельзя отметить собственную публикацию как понравившуюся.');
        }

        if ($post->getStatus()->isDraft()) {
            throw new DomainException('Ошибка добавления публикации в список понравившихся.');
        }

        $like = new Like(
            Id::next(),
            new AuthorId($command->author),
            new Entity(
                $command->entityType,
                $command->entityId
            )
        );

        $this->likes->add($like);

        $this->flusher->flush();
    }
}
