<?php

declare(strict_types=1);

namespace App\Model\Post\Entity\Post;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Entity
 * @ORM\Table(name="post_posts")
 */
class Post
{
    /**
     * @var Id
     * @ORM\Column(type="post_post_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var AuthorId
     * @ORM\Column(type="post_post_author_id")
     */
    private $authorId;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $alias;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", name="preview_text")
     */
    private $previewText;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var Status
     * @ORM\Column(type="post_post_status", name="status")
     */
    private $status;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", name="published_at", nullable=true)
     */
    private $publishedAt;

    /**
     * @param Id       $id
     * @param AuthorId $authorId
     * @param string   $alias
     * @param string   $title
     * @param string   $previewText
     * @param string   $text
     */
    public function __construct(
        Id $id,
        AuthorId $authorId,
        string $alias,
        string $title,
        string $previewText,
        string $text
    ) {
        $this->id          = $id;
        $this->authorId    = $authorId;
        $this->alias       = $alias;
        $this->title       = $title;
        $this->previewText = $previewText;
        $this->text        = $text;
        $this->status      = Status::draft();
        $this->createdAt   = new DateTimeImmutable();
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
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getPreviewText(): string
    {
        return $this->previewText;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * Публикация записи
     *
     * @param DateTimeImmutable|null $publishedAt
     */
    public function publish(DateTimeImmutable $publishedAt = null): void
    {
        if ($this->status->isPublish()) {
            throw new DomainException('Публикация уже опубликована.');
        }

        $this->status = Status::published();

        if (empty($this->publishedAt)) {

            if (is_null($publishedAt)) {
                $this->publishedAt = new DateTimeImmutable();
            } else {
                $this->publishedAt = $publishedAt;
            }
        }
    }

    /**
     * Перенос записи в черновики
     */
    public function draft(): void
    {
        if ($this->status->isDraft()) {
            throw new DomainException('Публикация уже находится в черновиках.');
        }

        $this->status = Status::draft();
    }

    /**
     * Редактирование публикации
     *
     * @param string $alias
     * @param string $title
     * @param string $previewText
     * @param string $text
     */
    public function edit(string $alias, string $title, string $previewText, string $text): void
    {
        $this->alias = $alias;
        $this->title = $title;
        $this->previewText = $previewText;
        $this->text = $text;
    }
}
