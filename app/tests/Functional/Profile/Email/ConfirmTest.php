<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Email;

use App\Tests\Fixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;

class ConfirmTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/email';

    /**
     * Переход по ссылке подтверждения пароля гостем
     */
    public function testShowGuest()
    {
        $this->get(self::BASE_URL . '/123', true);
        $this->assertCurrentUri('login');
    }

    /**
     * Переход по ссылке пользователем, который не запрашивал смену email
     */
    public function testChangingNotRequested()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL . '/123', true);

        $this->assertCurrentUri('profile');

        $this->assertDangerAlertContains('Изменение email не было запрошено.', $crawler);
    }

    /**
     * Переход по ссылке с неправильным токеном
     */
    public function testInvalidToken()
    {
        $this->auth(UserFixture::invalidTokenUserCredentials());
        $crawler = $this->get(self::BASE_URL . '/456', true);

        $this->assertCurrentUri('profile');

        $this->assertDangerAlertContains('Неверный токен изменения пароля.', $crawler);
    }

    /**
     * Успешное изменение email
     */
    public function testSuccess()
    {
        $this->auth(UserFixture::invalidTokenUserCredentials());
        $this->get(self::BASE_URL . '/123', true);
        $this->assertCurrentUri();

        $this->auth([
            'PHP_AUTH_USER' => 'test@test.ru',
            'PHP_AUTH_PW'   => '123qwe',
        ]);

        $crawler = $this->get('/profile');

        $this->assertResponseIsSuccessful();

        $this->assertContains('test@test.ru', $crawler->filter('body')->text());

        $this->assertIsInDatabase('user_users', [
            'email' => 'test@test.ru',
            'new_email' => null,
            'new_email_token' => null,
        ]);

        $this->assertIsNotInDatabase('user_users', [
            'email' => 'invalid-new-email-token-user@app.test',
            'new_email' => 'test@test.ru',
        ]);
    }
}
