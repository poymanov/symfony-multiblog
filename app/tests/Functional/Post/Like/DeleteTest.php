<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post\Like;

use App\Tests\Functional\DbWebTestCase;

class DeleteTest extends DbWebTestCase
{
    private const BASE_URL = '/posts/published-test-title/delete-like';

    /**
     * Гости не могут снимать лайки
     */
    public function testGuest()
    {
        $this->delete(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Нельзя снимать лайк с несуществующей публикации
     */
    public function testNotExisted()
    {
        $this->auth();
        $this->delete('/posts/123/delete-like');
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Нельзя снимать лайк с собственной публикации
     */
    public function testSelfPost()
    {
        $this->auth();
        $crawler = $this->delete('/posts/published-test/delete-like', true);
        $this->assertCurrentUri();
        $this->assertDangerAlertContains('Нельзя удалить собственную публикацию из списка понравившихся.', $crawler);
    }

    /**
     * Нельзя снимать лайк с неопубликованной публикации
     */
    public function testDraftPost()
    {
        $this->auth();
        $crawler = $this->delete('/posts/another-test/delete-like', true);
        $this->assertCurrentUri();
        $this->assertDangerAlertContains('Ошибка удаления публикации из списка понравившихся.', $crawler);
    }

    /**
     * Нельзя снимать с публикации, у которой нет лайка
     */
    public function testPostWithoutLike()
    {
        $this->auth();
        $crawler = $this->delete('/posts/published-test-title-2/delete-like', true);

        $this->assertCurrentUri();
        $this->assertDangerAlertContains('Публикация не добавлена в список понравившихся.', $crawler);
    }

    /**
     * Удаление лайка с понравившейся публикации
     */
    public function testDeleteLike()
    {
        $this->auth();
        $crawler = $this->delete(self::BASE_URL, true);
        $this->assertSuccessAlertContains('Публикация удалена из списка понравившихся.', $crawler);
    }

    /**
     * Гости не видят кнопку снятия лайка
     */
    public function testLikeButtonGuest()
    {
        $crawler = $this->get('/posts/published-test-title');

        $this->assertEquals(0, $crawler->filter('form[action="/posts/published-test-title/delete-like"]')->count());
    }

    /**
     * Кнопка снятия лайка для собственных публикаций отсутствует
     */
    public function testLikeButtonSelfPost()
    {
        $this->auth();
        $crawler = $this->get('/posts/published-test');

        $this->assertEquals(0, $crawler->filter('form[action="/posts/published-test/delete-like"]')->count());
    }

    /**
     * Кнопка снятия лайка присутствует для публикаций других пользователей
     */
    public function testDeleteLikeButtonPost()
    {
        $this->auth();
        $crawler = $this->get('/posts/published-test-title');

        $this->assertEquals(1, $crawler->filter('form[action="' . self::BASE_URL . '"]')->count());
    }
}
