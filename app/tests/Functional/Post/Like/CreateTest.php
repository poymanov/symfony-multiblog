<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post\Like;

use App\Tests\Functional\DbWebTestCase;

class CreateTest extends DbWebTestCase
{
    private const BASE_URL = '/posts/another-published-test/like';

    /**
     * Гости не могут ставить лайки
     */
    public function testGuest()
    {
        $this->post(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Нельзя ставить лайк несуществующей публикации
     */
    public function testNotExisted()
    {
        $this->auth();
        $this->post('/posts/123/like');
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Нельзя ставить лайк собственной публикации
     */
    public function testSelfPost()
    {
        $this->auth();
        $crawler = $this->post('/posts/published-test/like', true);
        $this->assertCurrentUri();
        $this->assertDangerAlertContains('Нельзя отметить собственную публикацию как понравившуюся.', $crawler);
    }

    /**
     * Нельзя ставить лайк неопубликованной публикации
     */
    public function testDraftPost()
    {
        $this->auth();
        $crawler = $this->post('/posts/another-test/like', true);
        $this->assertCurrentUri();
        $this->assertDangerAlertContains('Ошибка добавления публикации в список понравившихся.', $crawler);
    }

    /**
     * Успешная установка лайка
     */
    public function testSuccess()
    {
        $this->auth();
        $crawler = $this->post(self::BASE_URL, true);
        $this->assertSuccessAlertContains('Публикация отмечена как понравившаяся.', $crawler);
    }

    /**
     * Гости не видят кнопку лайка
     */
    public function testLikeButtonGuest()
    {
        $crawler = $this->get('/posts/published-test');

        $this->assertEquals(0, $crawler->filter('form[action="/posts/published-test/like"]')->count());
    }

    /**
     * Кнопка лайка для собственных публикаций отсутствует
     */
    public function testLikeButtonSelfPost()
    {
        $this->auth();
        $crawler = $this->get('/posts/published-test');

        $this->assertEquals(0, $crawler->filter('form[action="/posts/published-test/like"]')->count());
    }

    /**
     * Кнопка лайка присутствует для публикаций других пользователей
     */
    public function testLikeButtonPost()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test');

        $this->assertEquals(1, $crawler->filter('form[action="' . self::BASE_URL . '"]')->count());
    }
}
