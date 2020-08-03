<?php

namespace App\Tests\Functional\Home;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;

class MainTest extends DbWebTestCase
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

        $this->assertEquals(2, $crawler->filter('a[href="/signup"]')->count());
        $this->assertEquals(2, $crawler->filter('a[href="/login"]')->count());
        $this->assertEquals(0, $crawler->filter('a[href="/logout"]')->count());
        $this->assertEquals(0, $crawler->filter('a[href="/profile"]')->count());
    }

    /**
     * Просмотр главной страницы аутентифицированным пользователем
     */
    public function testIndexAuthUser()
    {
        $this->client->setServerParameters(UserFixture::userCredentials());

        $crawler = $this->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertEquals(0, $crawler->filter('a[href="/signup"]')->count());
        $this->assertEquals(0, $crawler->filter('a[href="/login"]')->count());
        $this->assertEquals(1, $crawler->filter('a[href="/logout"]')->count());
        $this->assertEquals(1, $crawler->filter('a[href="/profile"]')->count());
        $this->assertContains('First Last', $crawler->filter('body')->text());
    }
}
