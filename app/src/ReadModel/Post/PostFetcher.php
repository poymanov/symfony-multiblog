<?php

declare(strict_types=1);

namespace App\ReadModel\Post;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class PostFetcher
{
    /**
     * @var Connection;
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $id
     * @return array
     */
    public function allForUser(string $id): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'title',
                'status',
                'created_at as created',
                'published_at as published'
            )
            ->from('post_posts', 'c')
            ->andWhere('author_id = :author_id')
            ->setParameter(':author_id', $id)
            ->orderBy('created_at')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ProfileListView::class);

        return $stmt->fetchAll();
    }
}
