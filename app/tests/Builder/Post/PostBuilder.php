<?php

declare(strict_types=1);

namespace App\Tests\Builder\Post;

use App\Model\Post\Entity\Post\AuthorId;
use App\Model\Post\Entity\Post\Id;
use App\Model\Post\Entity\Post\Post;
use App\Model\Post\Entity\Post\Status;
use App\Tests\Builder\User\UserBuilder;
use Ausi\SlugGenerator\SlugGenerator;
use DateTimeImmutable;
use Faker;

class PostBuilder
{
    /**
     * @var Id
     */
    private $id;

    /**
     * @var AuthorId
     */
    private $authorId;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $previewText;

    /**
     * @var string
     */
    private $text;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    public function __construct()
    {
        $faker  = Faker\Factory::create();
        $author = (new UserBuilder())->viaEmail()->build();

        $this->id          = Id::next();
        $this->authorId    = new AuthorId($author->getId()->getValue());
        $this->createdAt   = new DateTimeImmutable();
        $this->title       = $faker->sentence(2);
        $this->previewText = $faker->text;
        $this->text        = $faker->text;
        $this->alias       = (new SlugGenerator())->generate($this->title);
    }

    /**
     * @return $this
     */
    public function draft(): self
    {
        $clone         = clone $this;
        $clone->status = Status::draft();

        return $clone;
    }

    /**
     * @return $this
     */
    public function published(): self
    {
        $clone         = clone $this;
        $clone->status = Status::published();

        return $clone;
    }

    /**
     * @param Id $id
     *
     * @return $this
     */
    public function withId(Id $id): self
    {
        $clone     = clone $this;
        $clone->id = $id;

        return $clone;
    }

    /**
     * @param AuthorId $authorId
     *
     * @return $this
     */
    public function withAuthorId(AuthorId $authorId): self
    {
        $clone           = clone $this;
        $clone->authorId = $authorId;

        return $clone;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function withTitle(string $title): self
    {
        $clone        = clone $this;
        $clone->title = $title;
        $clone->alias = (new SlugGenerator())->generate($title);

        return $clone;
    }

    /**
     * @param string $previewText
     *
     * @return $this
     */
    public function withPreviewText(string $previewText): self
    {
        $clone              = clone $this;
        $clone->previewText = $previewText;

        return $clone;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function withText(string $text): self
    {
        $clone       = clone $this;
        $clone->text = $text;

        return $clone;
    }

    /**
     * @return Post
     */
    public function build(): Post
    {
        $post = new Post(
            $this->id,
            $this->authorId,
            $this->alias,
            $this->title,
            $this->previewText,
            $this->text
        );

        if ($this->status->isPublish()) {
            $post->publish();
        }

        return $post;
    }

}
