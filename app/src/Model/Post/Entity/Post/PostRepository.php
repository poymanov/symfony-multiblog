<?php

declare(strict_types=1);

namespace App\Model\Post\Entity\Post;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PostRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repo;

    /**
     * UserRepository constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
        $this->repo = $em->getRepository(Post::class);
    }

    /**
     * @param string $alias
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByAlias(string $alias): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.alias = :alias')
                ->setParameter(':alias', $alias)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param string $alias
     *
     * @param string $id
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByAliasExceptId(string $alias, string $id): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.alias = :alias')
                ->andWhere('t.id != :id')
                ->setParameter(':alias', $alias)
                ->setParameter(':id', $id)
                ->getQuery()->getSingleScalarResult() > 0;
    }


    /**
     * @param Id $id
     *
     * @return Post
     */
    public function get(Id $id): Post
    {
        if (!$post = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Публикация не найдена.');
        }

        return $post;
    }

    /**
     * @param Post $post
     */
    public function add(Post $post): void
    {
        $this->em->persist($post);
    }
}
