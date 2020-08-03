<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Posts;

use App\Model\Post\Entity\Post\Id;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Fixtures\PostFixture;

class DraftTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/posts/draft/';
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
     * Попытка перевода в черновики несуществующего поста
     *
     * @throws Exception
     */
    public function testDraftNotExistedPost(): void
    {
        $this->auth();
        $this->patch(self::BASE_URL . Id::next());

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Попытка перевода в черновики чужой публикации
     *
     * @throws Exception
     */
    public function testDraftAnotherUserPost(): void
    {
        $this->auth();
        $this->patch(self::BASE_URL . PostFixture::POST_4_ID);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Попытка перевода в черновики через GET-запрос
     *
     * @throws Exception
     */
    public function testDraftGetRequest(): void
    {
        $this->auth();
        $this->get(self::POST_3_URL);

        $this->assertResponseStatusCodeSame(405);
    }

    /**
     * Попытка перевести в черновики запись, которая уже является черновиком
     */
    public function testDraftDrafted(): void
    {
        $this->auth();
        $crawler = $this->patch(self::BASE_URL . PostFixture::POST_1_ID, true);

        $this->assertCurrentUri('profile/posts');
        $this->assertDangerAlertContains('Публикация уже находится в черновиках.', $crawler);
    }

    /**
     * Успешный перевод записи в черновики
     */
    public function testSuccess(): void
    {
        $this->auth();
        $crawler = $this->patch(self::POST_3_URL, true);

        $this->assertCurrentUri('profile/posts');
        $this->assertSuccessAlertContains('Публикация переведена в черновики.', $crawler);
    }
}
