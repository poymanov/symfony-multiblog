<?php

declare(strict_types=1);

namespace App\ReadModel\Post;

use App\Model\Post\Entity\Post\Post;
use App\Model\Post\Entity\Post\Status;
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
                'p.id',
                'p.title',
                'p.status',
                '(SELECT COUNT(*) FROM like_likes WHERE entity_id = CAST(p.id as varchar) AND entity_type = \'' . Post::class .  '\') as likes',
                'p.created_at as created',
                'p.published_at as published'
            )
            ->from('post_posts p')
            ->andWhere('p.author_id = :author_id')
            ->setParameter(':author_id', $id)
            ->orderBy('p.created_at')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ProfileListView::class);

        return $stmt->fetchAll();
    }

    /**
     * @return array
     */
    public function getAllForMainPage(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'p.alias',
                'p.title',
                'p.preview_text as preview',
                'p.published_at as published',
                'TRIM(CONCAT(u.name_first, \' \', u.name_last)) as author',
                '(SELECT COUNT(*) FROM like_likes WHERE entity_id = CAST(p.id as varchar) AND entity_type = \'' . Post::class .  '\') as likes'
            )
            ->from('post_posts', 'p')
            ->innerJoin('p', 'user_users', 'u', 'p.author_id = u.id')
            ->where('p.status = :status')
            ->setParameter(':status', Status::STATUS_PUBLISHED)
            ->orderBy('p.published_at', 'DESC')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, MainPageView::class);

        return $stmt->fetchAll();
    }
}
