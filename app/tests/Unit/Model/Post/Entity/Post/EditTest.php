<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Post\Entity\Post;

use App\Tests\Builder\Post\PostBuilder;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    /**
     * Успешное редактирование записи
     */
    public function testSuccess()
    {
        $post = (new PostBuilder())->draft()->build();

        $post->edit(
            $alias = 'new alias',
            $title = 'new title',
            $previewText = 'preview text',
            $text = 'text',
        );

        self::assertEquals($alias, $post->getAlias());
        self::assertEquals($title, $post->getTitle());
        self::assertEquals($previewText, $post->getPreviewText());
        self::assertEquals($text, $post->getText());
    }
}
