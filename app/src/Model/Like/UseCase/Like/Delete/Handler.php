<?php

declare(strict_types=1);

namespace App\Model\Like\UseCase\Like\Delete;

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
     *
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $post = $this->posts->get(new PostId($command->entityId));

        if ($post->getAuthorId()->getValue() == $command->author) {
            throw new DomainException('Нельзя удалить собственную публикацию из списка понравившихся.');
        }

        if ($post->getStatus()->isDraft()) {
            throw new DomainException('Ошибка удаления публикации из списка понравившихся.');
        }

        $entity   = new Entity(
            $command->entityType,
            $command->entityId
        );

        $authorId = new AuthorId($command->author);

        try {
            $like = $this->likes->getByEntityAndAuthorId($entity, $authorId);
        } catch (\Throwable $e) {
            throw new DomainException('Публикация не добавлена в список понравившихся.');
        }

        $this->likes->remove($like);

        $this->flusher->flush();
    }
}
