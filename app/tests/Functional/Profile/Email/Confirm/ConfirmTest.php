<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Email\Confirm;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ConfirmTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/profile/email';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
        $this->alert = new AlertTestCaseHelper($this);
    }

    /**
     * Переход по ссылке подтверждения пароля гостем
     */
    public function testShowGuest()
    {
        $this->url->get(self::BASE_URL . '/123', true);
        $this->url->assertCurrentUri('login');
    }

    /**
     * Переход по ссылке пользователем, который не запрашивал смену email
     */
    public function testChangingNotRequested()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $crawler = $this->url->get(self::BASE_URL . '/123', true);

        $this->url->assertCurrentUri('profile');

        $this->alert->assertDangerAlertContains('Изменение email не было запрошено.', $crawler);
    }

    /**
     * Переход по ссылке с неправильным токеном
     */
    public function testInvalidToken()
    {
        $this->loadFixtures([ConfirmFixture::class]);

        $this->client->setServerParameters(ConfirmFixture::userCredentials());
        $crawler = $this->url->get(self::BASE_URL . '/456', true);

        $this->url->assertCurrentUri('profile');

        $this->alert->assertDangerAlertContains('Неверный токен изменения пароля.', $crawler);
    }

    /**
     * Успешное изменение email
     */
    public function testSuccess()
    {
        $this->loadFixtures([ConfirmFixture::class]);

        $this->client->setServerParameters(ConfirmFixture::userCredentials());
        $this->url->get(self::BASE_URL . '/123', true);
        $this->url->assertCurrentUri();

        $this->client->setServerParameters([
            'PHP_AUTH_USER' => 'test@test.ru',
            'PHP_AUTH_PW' => '123qwe',
        ]);

        $crawler = $this->url->get('/profile');

        $this->assertResponseIsSuccessful();

        $this->assertContains('test@test.ru', $crawler->filter('body')->text());
    }
}
