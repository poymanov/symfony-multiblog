<?php

declare(strict_types=1);

namespace App\Model\Comment\UseCase\Comment\Remove;

use App\Model\Comment\Entity\Comment\AuthorId;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\CommentRepository;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Flusher;
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

    /**
     * @param CommentRepository $comments
     * @param Flusher $flusher
     */
    public function __construct(CommentRepository $comments, Flusher $flusher)
    {
        $this->comments = $comments;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $comment = $this->comments->get(new Id($command->id));

        if (!$comment->canDelete()) {
            throw new DomainException('Удаление запрещено. С момента создания комментария прошло более 24 часов.');
        }

        $this->comments->remove($comment);

        $this->flusher->flush();
    }
}
