<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Posts;

use App\Model\Post\Entity\Post\Id;
use App\Tests\Fixtures\PostFixture;
use App\Tests\Functional\DbWebTestCase;
use Exception;

class PublishTest extends DbWebTestCase
{
    private const BASE_URL   = '/profile/posts/publish/';
    private const POST_3_URL = self::BASE_URL . PostFixture::POST_3_ID;

    /**
     * Для гостей страница недоступна
     */
    public function testShowGuest(): void
    {
        $this->patch(self::POST_3_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Попытка публикации несуществующего поста
     *
     * @throws Exception
     */
    public function testPublishNotExistedPost(): void
    {
        $this->auth();
        $this->patch(self::BASE_URL . Id::next());

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Попытка публикации чужой публикации
     *
     * @throws Exception
     */
    public function testPublishAnotherUserPost(): void
    {
        $this->auth();
        $this->patch(self::BASE_URL . PostFixture::POST_2_ID);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Попытка публикации через GET-запрос
     *
     * @throws Exception
     */
    public function testPublishGetRequest(): void
    {
        $this->auth();
        $this->get(self::POST_3_URL);

        $this->assertResponseStatusCodeSame(405);
    }

    /**
     * Попытка опубликовать уже опубликованную запись
     */
    public function testPublishPublished(): void
    {
        $this->auth();
        $crawler = $this->patch(self::POST_3_URL, true);

        $this->assertCurrentUri('profile/posts');
        $this->assertDangerAlertContains('Публикация уже опубликована.', $crawler);
    }

    /**
     * Успешная публикация записи
     */
    public function testSuccess(): void
    {
        $this->auth();
        $crawler = $this->patch(self::BASE_URL . PostFixture::POST_1_ID, true);

        $this->assertCurrentUri('profile/posts');
        $this->assertSuccessAlertContains('Публикация опубликована.', $crawler);

        $this->assertIsInDatabase('post_posts', [
            'id'     => PostFixture::POST_1_ID,
            'status' => 'published',
        ]);
    }
}
