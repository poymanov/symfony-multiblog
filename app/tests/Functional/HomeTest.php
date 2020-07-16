<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class HomeTest extends DbWebTestCase
{
    use FixturesTrait;

    private UrlTestCaseHelper $url;

    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
    }

    /**
     * Открытие главной страницы
     */
    public function testIndex()
    {
        $crawler = $this->url->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertContains('Блоги обо всём', $crawler->filter('h1')->text());
    }

    /**
     * Просмотр главной страницы гостём
     */
    public function testIndexGuest()
    {
        $crawler = $this->url->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertCount(2, $crawler->filter('a[href="/signup"]'));
        $this->assertCount(2, $crawler->filter('a[href="/login"]'));
    }

    /**
     * Просмотр главной страницы аутентифицированным пользователем
     */
    public function testIndexAuthUser()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());

        $crawler = $this->url->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertCount(0, $crawler->filter('a[href="/signup"]'));
        $this->assertCount(0, $crawler->filter('a[href="/login"]'));
    }
}
