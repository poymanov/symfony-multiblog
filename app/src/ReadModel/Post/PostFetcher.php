<?php

declare(strict_types=1);

namespace App\ReadModel\Post;

use App\Model\Post\Entity\Post\Post;
use App\Model\Post\Entity\Post\Status;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $repository;

    /**
     * @param Connection         $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $connection, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->paginator  = $paginator;
        $this->repository = $em->getRepository(Post::class);
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
                '(SELECT COUNT(*) FROM comment_comments WHERE entity_id = CAST(p.id as varchar) AND entity_type = \'' . Post::class . '\') as comments',
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
                'u.alias as author_alias',
                '(SELECT COUNT(*) FROM like_likes WHERE entity_id = CAST(p.id as varchar) AND entity_type = \'' . Post::class . '\') as likes',
                '(SELECT COUNT(*) FROM comment_comments WHERE entity_id = CAST(p.id as varchar) AND entity_type = \'' . Post::class . '\') as comments',
            )
            ->from('post_posts', 'p')
            ->innerJoin('p', 'user_users', 'u', 'p.author_id = u.id')
            ->where('p.status = :status')
            ->setParameter(':status', Status::STATUS_PUBLISHED)
            ->orderBy('p.published_at', 'DESC');

        return $this->paginator->paginate($qb, $page, $perPage);
    }

    /**
     * Сколько лайков поставили публикациям пользователя
     *
     * @param string $userId
     *
     * @return int
     */
    public function countLikesForUserPosts(string $userId): int
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('like_likes', 'l')
                ->innerJoin('l', 'post_posts', 'p', 'l.entity_id = CAST(p.id as varchar)')
                ->where('l.entity_type = :entity_type')
                ->andWhere('p.author_id = :author_id')
                ->setParameter(':entity_type', Post::class)
                ->setParameter(':author_id', $userId)
                ->execute()->fetchColumn();
    }

    /**
     * Сколько комментариев оставили публикациям пользователя
     *
     * @param string $userId
     *
     * @return int
     */
    public function countCommentsForUserPosts(string $userId): int
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('comment_comments', 'c')
            ->innerJoin('c', 'post_posts', 'p', 'c.entity_id = CAST(p.id as varchar)')
            ->where('c.entity_type = :entity_type')
            ->andWhere('p.author_id = :author_id')
            ->setParameter(':entity_type', Post::class)
            ->setParameter(':author_id', $userId)
            ->execute()->fetchColumn();
    }

    /**
     * @param string $id
     *
     * @return Post
     */
    public function get(string $id): Post
    {
        if (!$post = $this->repository->find($id)) {
            throw new NotFoundException('Публикация не найдена.');
        }

        return $post;
    }
}
