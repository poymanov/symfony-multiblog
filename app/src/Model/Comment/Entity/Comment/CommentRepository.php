<?php

declare(strict_types=1);

namespace App\Model\Comment\Entity\Comment;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class CommentRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $repo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Comment::class);
    }

    /**
     * @param Id $id
     * @return Comment
     */
    public function get(Id $id): Comment
    {
        /** @var Comment $comment */
        if (!$comment = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Комментарий не найден.');
        }

        return $comment;
    }

    /**
     * @param Comment $comment
     */
    public function add(Comment $comment): void
    {
        $this->em->persist($comment);
    }

    /**
     * @param Comment $comment
     */
    public function remove(Comment $comment): void
    {
        $this->em->remove($comment);
    }
}
