<?php

declare(strict_types=1);

namespace App\ReadModel\Comment;

use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\Entity;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class CommentFetcher
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * @var EntityManagerInterface
     */
    private $repository;

    /**
     * @param Connection             $connection
     * @param PaginatorInterface     $paginator
     * @param EntityManagerInterface $em
     */
    public function __construct(Connection $connection, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->paginator  = $paginator;
        $this->repository = $em->getRepository(Comment::class);
    }

    /**
     * @param Entity $entity
     * @param int    $page
     * @param int    $perPage
     *
     * @return PaginationInterface
     */
    public function allForEntity(Entity $entity, int $page, int $perPage): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'c.id',
                'c.text',
                'c.author_id',
                'c.entity_type',
                'c.entity_id',
                'TRIM(CONCAT(u.name_first, \' \', u.name_last)) as author',
                'u.alias as author_alias',
                'c.created_at',
                'c.updated_at',
            )
            ->from('comment_comments', 'c')
            ->innerJoin('c', 'user_users', 'u', 'c.author_id = u.id')
            ->where('entity_type = :entity_type')
            ->andWhere('entity_id = :entity_id')
            ->setParameter(':entity_type', $entity->getType())
            ->setParameter(':entity_id', $entity->getId())
            ->orderBy('c.created_at');

        return $this->paginator->paginate($qb, $page, $perPage);
    }

    /**
     * @param string $id
     *
     * @return Comment
     */
    public function get(string $id): Comment
    {
        if (!$comment = $this->repository->find($id)) {
            throw new NotFoundException('Комментарий не найден.');
        }

        return $comment;
    }
}
