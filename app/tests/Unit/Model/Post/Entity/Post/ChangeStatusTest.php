<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Post\Entity\Post;

use App\Tests\Builder\Post\PostBuilder;
use PHPUnit\Framework\TestCase;

class ChangeStatusTest extends TestCase
{
    /**
     * Публикация уже находится в черновиках
     */
    public function testAlreadyDraft()
    {
        $post = (new PostBuilder())->draft()->build();

        $this->expectExceptionMessage('Публикация уже находится в черновиках.');
        $post->draft();
    }

    /**
     * Публикация перемещена в черновики
     */
    public function testDrafted()
    {
        $post = (new PostBuilder())->published()->build();

        $post->draft();

        self::assertTrue($post->getStatus()->isDraft());
    }

    /**
     * Публикация уже находится опубликована
     */
    public function testAlreadyPublished()
    {
        $post = (new PostBuilder())->published()->build();

        $this->expectExceptionMessage('Публикация уже опубликована.');
        $post->publish();
    }

    /**
     * Публикация опубликована
     */
    public function testPublished()
    {
        $post = (new PostBuilder())->draft()->build();

        $post->publish();
        self::assertTrue($post->getStatus()->isPublish());
        self::assertNotNull($post->getPublishedAt());
    }

    /**
     * Дата публикации не меняется после повторной публикации
     */
    public function testSamePublishDate()
    {
        $post = (new PostBuilder())->published()->build();

        $publishedDate = $post->getPublishedAt();

        $post->draft();
        $post->publish();

        self::assertEquals($publishedDate, $post->getPublishedAt());
    }
}
