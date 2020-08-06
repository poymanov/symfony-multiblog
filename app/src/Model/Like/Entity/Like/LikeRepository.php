<?php

declare(strict_types=1);

namespace App\Model\Like\Entity\Like;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class LikeRepository
{
    /**
     * @var EntityRepository
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
        $this->em   = $em;
        $this->repo = $em->getRepository(Like::class);
    }

    /**
     * @param Id $id
     *
     * @return Like
     */
    public function get(Id $id): Like
    {
        /** @var Like $like */
        if (!$like = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Лайк не найден.');
        }

        return $like;
    }

    /**
     * @param Entity   $entity
     * @param AuthorId $authorId
     *
     * @return Like
     */
    public function getByEntityAndAuthorId(Entity $entity, AuthorId $authorId): Like
    {
        /** @var Like $like */
        if (!$like = $this->repo->findOneBy([
            'entity.type' => $entity->getType(),
            'entity.id'   => $entity->getId(),
            'authorId'    => $authorId->getValue(),
        ])) {
            throw new EntityNotFoundException('Лайк не найден.');
        }

        return $like;
    }

    /**
     * @param Like $like
     */
    public function add(Like $like): void
    {
        $this->em->persist($like);
    }

    /**
     * @param Like $like
     */
    public function remove(Like $like): void
    {
        $this->em->remove($like);
    }
}
