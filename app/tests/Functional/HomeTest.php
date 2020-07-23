<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixture;

class HomeTest extends DbWebTestCase
{
    /**
     * Открытие главной страницы
     */
    public function testIndex()
    {
        $crawler = $this->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertContains('Блоги обо всём', $crawler->filter('h1')->text());
    }

    /**
     * Просмотр главной страницы гостём
     */
    public function testIndexGuest()
    {
        $crawler = $this->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertCount(2, $crawler->filter('a[href="/signup"]'));
        $this->assertCount(2, $crawler->filter('a[href="/login"]'));
        $this->assertCount(0, $crawler->filter('a[href="/logout"]'));
        $this->assertCount(0, $crawler->filter('a[href="/profile"]'));
    }

    /**
     * Просмотр главной страницы аутентифицированным пользователем
     */
    public function testIndexAuthUser()
    {
        $this->client->setServerParameters(UserFixture::userCredentials());

        $crawler = $this->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertCount(0, $crawler->filter('a[href="/signup"]'));
        $this->assertCount(0, $crawler->filter('a[href="/login"]'));
        $this->assertCount(1, $crawler->filter('a[href="/logout"]'));
        $this->assertCount(1, $crawler->filter('a[href="/profile"]'));
        $this->assertContains('First Last', $crawler->filter('body')->text());
    }
}
