<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Posts;

use App\DataFixtures\UserFixture;
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
        $this->client->setServerParameters(UserFixture::userCredentials());
        $crawler = $this->get(self::BASE_URL);

        $this->assertEquals(1, $crawler->filter('a[href="/profile/posts/create"]')->count());
    }

    /**
     * Отображение публикаций
     */
    public function testShowPosts()
    {
        $this->client->setServerParameters(UserFixture::userCredentials());
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
        $this->client->setServerParameters(UserFixture::userCredentials());
        $crawler = $this->get(self::BASE_URL);
        $this->assertEquals(2, $crawler->filterXPath('//a[contains(@href, "/profile/posts/edit/")]')->count());
    }
}