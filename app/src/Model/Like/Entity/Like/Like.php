<?php

declare(strict_types=1);

namespace App\Model\Like\Entity\Like;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="like_likes", uniqueConstraints={
 *      @ORM\UniqueConstraint(columns={"author_id", "entity_type", "entity_id"})
 * })
 */
class Like
{
    /**
     * @var Id
     * @ORM\Column(type="like_like_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var AuthorId
     * @ORM\Column(type="like_like_author_id")
     */
    private $authorId;

    /**
     * @var Entity
     * @ORM\Embedded(class="Entity")
     */
    private $entity;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true, name="created_at")
     */
    private $createdAt;

    /**
     * @param Id       $id
     * @param AuthorId $authorId
     * @param Entity   $entity
     */
    public function __construct(Id $id, AuthorId $authorId, Entity $entity)
    {
        $this->id        = $id;
        $this->authorId  = $authorId;
        $this->entity    = $entity;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return AuthorId
     */
    public function getAuthorId(): AuthorId
    {
        return $this->authorId;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
