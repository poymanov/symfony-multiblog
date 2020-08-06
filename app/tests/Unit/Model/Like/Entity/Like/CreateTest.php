<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Like\Entity\Like;

use App\Model\Like\Entity\Like\AuthorId;
use App\Model\Like\Entity\Like\Entity;
use App\Model\Like\Entity\Like\Id;
use App\Model\Like\Entity\Like\Like;
use App\Model\Post\Entity\Post\Post;
use App\Tests\Builder\Post\PostBuilder;
use App\Tests\Builder\User\UserBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    /**
     * Успешное создание лайка пользователя для публикации
     *
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $author = (new UserBuilder())->viaEmail()->build();
        $post = (new PostBuilder())->published()->build();

        $like = new Like(
            $id = Id::next(),
            $authorId = new AuthorId($author->getId()->getValue()),
            $entity = new Entity(Post::class, $post->getId()->getValue())
        );

        self::assertEquals($id, $like->getId());
        self::assertEquals($authorId, $like->getAuthorId());
        self::assertEquals($entity, $like->getEntity());
    }
}
