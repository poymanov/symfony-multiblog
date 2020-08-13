<?php

declare(strict_types=1);

namespace App\Model\Comment\Entity\Comment;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment_comments", indexes={
 *      @ORM\Index(columns={"created_at"}),
 *      @ORM\Index(columns={"entity_type", "entity_id"})
 * })
 */
class Comment
{
    /**
     * @var Id
     * @ORM\Column(type="comment_comment_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var AuthorId
     * @ORM\Column(type="comment_comment_author_id")
     */
    private $authorId;

    /**
     * @var Entity
     * @ORM\Embedded(class="Entity")
     */
    private $entity;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true, name="created_at")
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true, name="updated_at")
     */
    private $updatedAt;

    /**
     * @param Id                $id
     * @param AuthorId          $authorId
     * @param Entity            $entity
     * @param string            $text
     * @param DateTimeImmutable $createdAt
     */
    public function __construct(Id $id, AuthorId $authorId, Entity $entity, string $text, DateTimeImmutable $createdAt)
    {
        $this->id        = $id;
        $this->authorId  = $authorId;
        $this->entity    = $entity;
        $this->text      = $text;
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTimeImmutable $date
     * @param string            $text
     */
    public function edit(DateTimeImmutable $date, string $text): void
    {
        if (!$this->canEdit()) {
            throw new DomainException('Редактирование запрещено. С момента создания комментария прошло более 24 часов.');
        }

        if ($this->text == $text) {
            throw new DomainException('Ошибка редактирования. Текст комментария не изменен.');
        }

        $this->updatedAt = $date;
        $this->text      = $text;
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
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Проверка: доступно ли редактирование комментария
     *
     * @return bool
     */
    public function canEdit(): bool
    {
        // Если с момента создания публикации не прошло 24 часа
        return (new DateTimeImmutable())->diff($this->createdAt)->days == 0;
    }

    /**
     * Проверка: доступно ли удаление комментария
     *
     * @return bool
     */
    public function canDelete(): bool
    {
        // Если с момента создания публикации не прошло 24 часа
        return (new DateTimeImmutable())->diff($this->createdAt)->days == 0;
    }
}
