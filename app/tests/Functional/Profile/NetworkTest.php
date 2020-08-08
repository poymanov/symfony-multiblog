<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile;

use App\Tests\Fixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;

class NetworkTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/social';

    /**
     * Для пользователя без подключенного Facebook отображать кнопку добавления соц. сети
     */
    public function testShowAddFacebookNetwork()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertContains('Подключить Facebook', $crawler->filter('body')->text());
        $this->assertEquals(1, $crawler->filter('a[href="/attach"]')->count());
    }

    /**
     * Для пользователя с подключенным Facebook отображать кнопку его отключения
     */
    public function testShowRemoveFacebookNetwork()
    {
        $this->auth(UserFixture::networkUserCredentials());
        $crawler = $this->get(self::BASE_URL);

        $this->assertNotContains('Подключить Facebook', $crawler->filter('body')->text());
        $this->assertContains('Отключить Facebook', $crawler->filter('body')->text());

        $this->assertEquals(0, $crawler->filter('a[href="/attach"]')->count());
        $this->assertEquals(1, $crawler->filter('form[action="http://localhost/profile/oauth/detach/facebook/0001"]')->count());
    }

    /**
     * Отключение Facebook
     */
    public function testDetachFacebook()
    {
        $this->auth(UserFixture::networkUserCredentials());
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getDetachData(), true);

        $this->assertCurrentUri('profile/social');
        $this->assertSuccessAlertContains('Facebook отключен.', $crawler);
    }

    /**
     * Получение данных для запроса отключения Facebook
     *
     * @return FormDataDto
     */
    private function getDetachData(): FormDataDto
    {
        $data = [];

        return new FormDataDto($data, 'Отключить Facebook');
    }
}
