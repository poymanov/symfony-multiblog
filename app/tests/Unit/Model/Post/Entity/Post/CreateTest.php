<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Post\Entity\Post;

use App\Model\Post\Entity\Post\AuthorId;
use App\Model\Post\Entity\Post\Id;
use App\Model\Post\Entity\Post\Post;
use App\Model\Post\Entity\Post\Status;
use App\Tests\Builder\User\UserBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    /**
     * Успешное создания объекта для публикации
     *
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $author = (new UserBuilder())->viaEmail()->build();

        $post = new Post(
            $id = Id::next(),
            $authorId = new AuthorId($author->getId()->getValue()),
            $alias = 'alias',
            $title = 'title',
            $previewText = 'preview text',
            $text = 'text',
        );

        self::assertEquals($id, $post->getId());
        self::assertEquals($authorId, $post->getAuthorId());
        self::assertEquals($alias, $post->getAlias());
        self::assertEquals($title, $post->getTitle());
        self::assertEquals($previewText, $post->getPreviewText());
        self::assertEquals($text, $post->getText());
        self::assertTrue($post->getStatus()->isEqual(new Status(Status::STATUS_DRAFT)));
        self::assertNull($post->getPublishedAt());
    }
}
