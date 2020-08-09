<?php

declare(strict_types=1);

namespace App\ReadModel\Post;

use App\Model\Post\Entity\Post\Post;
use App\Model\Post\Entity\Post\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PostFetcher
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
     * @param Connection         $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator  = $paginator;
    }

    /**
     * @param string $id
     *
     * @param int    $page
     * @param int    $perPage
     *
     * @return PaginationInterface
     */
    public function allForUser(string $id, int $page, int $perPage): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.title',
                'p.status',
                '(SELECT COUNT(*) FROM like_likes WHERE entity_id = CAST(p.id as varchar) AND entity_type = \'' . Post::class . '\') as likes',
                'p.created_at as created',
                'p.published_at as published'
            )
            ->from('post_posts p')
            ->andWhere('p.author_id = :author_id')
            ->setParameter(':author_id', $id)
            ->orderBy('p.created_at');

        return $this->paginator->paginate($qb, $page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @return PaginationInterface
     */
    public function getAllForMainPage(int $page, int $perPage): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.alias',
                'p.title',
                'p.preview_text as preview',
                'p.published_at as published',
                'TRIM(CONCAT(u.name_first, \' \', u.name_last)) as author',
                '(SELECT COUNT(*) FROM like_likes WHERE entity_id = CAST(p.id as varchar) AND entity_type = \'' . Post::class . '\') as likes'
            )
            ->from('post_posts', 'p')
            ->innerJoin('p', 'user_users', 'u', 'p.author_id = u.id')
            ->where('p.status = :status')
            ->setParameter(':status', Status::STATUS_PUBLISHED)
            ->orderBy('p.published_at', 'DESC');

        return $this->paginator->paginate($qb, $page, $perPage);
    }
}
