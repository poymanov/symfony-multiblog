<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile;

use App\Tests\Functional\DbWebTestCase;

class ProfileTest extends DbWebTestCase
{
    private const BASE_URL = '/profile';

    /**
     * Профиль недоступен гостям
     */
    public function testShowProfileGuest()
    {
        $this->get(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Профиль доступен аутентифицированному пользователю
     */
    public function testShowAuthUserProfile()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertResponseIsSuccessful();

        $this->assertContains('Личный кабинет', $crawler->filter('h1')->text());
        $this->assertContains('user@app.test', $crawler->filter('body')->text());
        $this->assertContains('First', $crawler->filter('body')->text());
        $this->assertContains('Last', $crawler->filter('body')->text());
    }
}
