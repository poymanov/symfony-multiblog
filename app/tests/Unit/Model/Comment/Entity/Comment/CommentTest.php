<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Comment\Entity\Comment;

use App\Model\Comment\Entity\Comment\AuthorId;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Post\Entity\Post\Post;
use App\Tests\Builder\Post\PostBuilder;
use App\Tests\Builder\User\UserBuilder;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    /**
     * Успешное создание
     *
     * @throws Exception
     */
    public function testCreate()
    {
        $author = (new UserBuilder())->viaEmail()->build();
        $post   = (new PostBuilder())->published()->build();

        $comment = new Comment(
            $id = Id::next(),
            $authorId = new AuthorId($author->getId()->getValue()),
            $entity = new Entity(Post::class, $post->getId()->getValue()),
            $text = 'test',
            $createdAt = new DateTimeImmutable(),
        );

        self::assertEquals($id, $comment->getId());
        self::assertEquals($authorId, $comment->getAuthorId());
        self::assertEquals($entity, $comment->getEntity());
        self::assertEquals($text, $comment->getText());
        self::assertEquals($createdAt, $comment->getCreatedAt());
    }

    /**
     * Успешное редактирование
     *
     * @throws Exception
     */
    public function testEdit()
    {
        $author = (new UserBuilder())->viaEmail()->build();
        $post   = (new PostBuilder())->published()->build();

        $comment = new Comment(
            Id::next(),
            new AuthorId($author->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue()),
            'test',
            new DateTimeImmutable(),
        );

        $comment->edit(
            $updatedAt = new DateTimeImmutable('+1 minute'),
            $text = 'test2'
        );

        self::assertEquals($text, $comment->getText());
        self::assertEquals($updatedAt, $comment->getUpdatedAt());

        $comment = new Comment(
            Id::next(),
            new AuthorId($author->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue()),
            'test',
            new DateTimeImmutable('-2 days'),
        );

        $this->expectExceptionMessage('Редактирование запрещено. С момента создания комментария прошло более 24 часов.');
        $comment->edit(new DateTimeImmutable('+1 minute'), 'test2');
    }

    /**
     * Проверка возможности редактирования публикации
     */
    public function testCanEdit()
    {
        $author = (new UserBuilder())->viaEmail()->build();
        $post   = (new PostBuilder())->published()->build();

        $comment = new Comment(
            Id::next(),
            new AuthorId($author->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue()),
            'test',
            new DateTimeImmutable(),
        );

        self::assertTrue($comment->canEdit());

        $comment = new Comment(
            Id::next(),
            new AuthorId($author->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue()),
            'test',
            new DateTimeImmutable('-2 days'),
        );

        self::assertFalse($comment->canEdit());
    }

    /**
     * Редактирование с текстом без изменений
     */
    public function testEditSameText()
    {
        $author = (new UserBuilder())->viaEmail()->build();
        $post   = (new PostBuilder())->published()->build();

        $comment = new Comment(
            Id::next(),
            new AuthorId($author->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue()),
            'test',
            new DateTimeImmutable(),
        );

        $this->expectExceptionMessage('Ошибка редактирования. Текст комментария не изменен.');
        $comment->edit(new DateTimeImmutable('+1 minute'), 'test');
    }

    public function testCanDelete()
    {
        $author = (new UserBuilder())->viaEmail()->build();
        $post   = (new PostBuilder())->published()->build();

        $comment = new Comment(
            Id::next(),
            new AuthorId($author->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue()),
            'test',
            new DateTimeImmutable(),
        );

        self::assertTrue($comment->canDelete());

        $comment = new Comment(
            Id::next(),
            new AuthorId($author->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue()),
            'test',
            new DateTimeImmutable('-2 days'),
        );

        self::assertFalse($comment->canDelete());
    }
}
