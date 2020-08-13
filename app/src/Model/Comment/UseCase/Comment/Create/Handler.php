<?php

declare(strict_types=1);

namespace App\Model\Comment\UseCase\Comment\Create;

use App\Model\Comment\Entity\Comment\AuthorId;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\CommentRepository;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Post\Entity\Post\Id as PostId;
use App\Model\Flusher;
use App\Model\Post\Entity\Post\PostRepository;
use DateTimeImmutable;
use DomainException;
use Exception;

class Handler
{
    /**
     * @var CommentRepository
     */
    private $comments;

    /**
     * @var Flusher
     */
    private $flusher;

    private PostRepository $posts;

    /**
     * @param CommentRepository $comments
     * @param Flusher           $flusher
     * @param PostRepository    $posts
     */
    public function __construct(CommentRepository $comments, Flusher $flusher, PostRepository $posts)
    {
        $this->comments = $comments;
        $this->flusher  = $flusher;
        $this->posts    = $posts;
    }

    /**
     * @param Command $command
     *
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $post = $this->posts->get(new PostId($command->entityId));

        if (!$post) {
            throw new DomainException('Публикация не найдена.');
        }

        if ($post->getStatus()->isDraft()) {
            throw new DomainException('Ошибка добавления комментария.');
        }

        $comment = new Comment(
            Id::next(),
            new AuthorId($command->author),
            new Entity(
                $command->entityType,
                $command->entityId
            ),
            $command->text,
            new DateTimeImmutable(),
        );

        $this->comments->add($comment);

        $this->flusher->flush();
    }
}
