<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Posts;

use App\Tests\Fixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;

class ListTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/posts';

    /**
     * Для гостей страница недоступна
     */
    public function testShowGuest(): void
    {
        $this->get(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Доступна кнопка добавления новой публикации
     */
    public function testCreateButton(): void
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertEquals(1, $crawler->filter('a[href="/profile/posts/create"]')->count());
    }

    /**
     * Отображение публикаций
     */
    public function testShowPosts()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertContains('Test', $crawler->filter('body')->text());
        $this->assertContains('Published Test', $crawler->filter('body')->text());
        $this->assertContains('Опубликовано', $crawler->filter('body')->text());
        $this->assertContains('Черновик', $crawler->filter('body')->text());
    }

    /**
     * Отображение ссылок на страницу редактирования публикаций
     */
    public function testEditPostLinks()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);
        $this->assertEquals(2, $crawler->filterXPath('//a[contains(@href, "/profile/posts/edit/")]')->count());
    }

    /**
     * Отображение количества лайков для публикаций
     */
    public function testShowPostsLikes()
    {
        $this->auth(UserFixture::testUserCredentials());
        $crawler = $this->get(self::BASE_URL);
        $this->assertEquals(1, $crawler->filter('tbody tr td:nth-child(3)')->text());
        $this->assertEquals(0, $crawler->filter('tbody tr:nth-child(2) td:nth-child(3)')->text());
        $this->assertEquals(0, $crawler->filter('tbody tr:nth-child(3) td:nth-child(3)')->text());
    }

    /**
     * Публикации отображаются с учетом пагинации
     */
    public function testPagination()
    {
        $this->auth(UserFixture::userWithManyPostsCredentials());
        $crawler = $this->get(self::BASE_URL);

        $this->assertEquals(20, $crawler->filter('tbody tr')->count());
        $this->assertEquals(2, $crawler->filter('a[href="' . self::BASE_URL .'?page=2"]')->count());
    }
}
