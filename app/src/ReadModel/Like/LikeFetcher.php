<?php

declare(strict_types=1);

namespace App\ReadModel\Like;

use App\Model\Like\Entity\Like\Like;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class LikeFetcher
{
    /**
     * @var Connection;
     */
    private $connection;

    /**
     * @var EntityManagerInterface
     */
    private $repository;

    /**
     * @param Connection             $connection
     * @param EntityManagerInterface $em
     */
    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Like::class);
    }

    /**
     * Существует ли лайк для типа по id и id автора
     *
     * @param string $entityType
     * @param string $entityId
     * @param string $authorId
     *
     * @return bool
     */
    public function existsByEntityAndAuthorId(string $entityType, string $entityId, string $authorId): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('like_likes')
                ->where('entity_type = :entity_type')
                ->andWhere('entity_id = :entity_id')
                ->andWhere('author_id = :author_id')
                ->setParameter(':entity_type', $entityType)
                ->setParameter(':entity_id', $entityId)
                ->setParameter(':author_id', $authorId)
                ->execute()->fetchColumn() > 0;
    }

    /**
     * Сколько лайков для типа по id
     *
     * @param string $entityType
     * @param string $entityId
     *
     * @return int
     */
    public function countByEntity(string $entityType, string $entityId): int
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('like_likes')
                ->where('entity_type = :entity_type')
                ->andWhere('entity_id = :entity_id')
                ->setParameter(':entity_type', $entityType)
                ->setParameter(':entity_id', $entityId)
                ->execute()->fetchColumn();
    }
}
