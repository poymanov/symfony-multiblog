<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Network;

use App\DataFixtures\UserFixture;
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
        $this->client->setServerParameters(UserFixture::userCredentials());
        $crawler = $this->get(self::BASE_URL);

        $this->assertContains('Подключить Facebook', $crawler->filter('body')->text());
        $this->assertCount(1, $crawler->filter('a[href="/attach"]'));
    }

    /**
     * Для пользователя с подключенным Facebook отображать кнопку его отключения
     */
    public function testShowRemoveFacebookNetwork()
    {
        $this->client->setServerParameters(NetworkFixture::userCredentials());
        $crawler = $this->get(self::BASE_URL);

        $this->assertNotContains('Подключить Facebook', $crawler->filter('body')->text());
        $this->assertContains('Отключить Facebook', $crawler->filter('body')->text());

        $this->assertCount(0, $crawler->filter('a[href="/attach"]'));
        $this->assertCount(1, $crawler->filter('form[action="http://localhost/profile/oauth/detach/facebook/0001"]'));
    }

    /**
     * Отключение Facebook
     */
    public function testDetachFacebook()
    {
        $this->client->setServerParameters(NetworkFixture::userCredentials());
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
