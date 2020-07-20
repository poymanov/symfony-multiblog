<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class ProfileTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/profile';

    private UrlTestCaseHelper $url;

    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
    }

    /**
     * Профиль недоступен гостям
     */
    public function testShowProfileGuest()
    {
        $this->url->get(self::BASE_URL, true);
        $this->url->assertCurrentUri('login');
    }

    /**
     * Профиль доступен аутентифицированному пользователю
     */
    public function testShowAuthUserProfile()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $crawler = $this->url->get(self::BASE_URL);

        $this->assertResponseIsSuccessful();

        $this->assertContains('Личный кабинет', $crawler->filter('h1')->text());
        $this->assertContains('user@app.test', $crawler->filter('body')->text());
        $this->assertContains('First', $crawler->filter('body')->text());
        $this->assertContains('Last', $crawler->filter('body')->text());
    }
}
