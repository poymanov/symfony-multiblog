<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\SignUp\Confirm;

use App\Tests\Functional\DbWebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ConfirmTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/signup';

    /**
     * Успешное подтверждение регистрации
     */
    public function testSuccess(): void
    {
        $this->loadFixtures([ConfirmFixture::class]);

        $crawler = $this->get(self::BASE_URL . '/not-confirmed-token', true);
        $this->assertResponseIsSuccessful();

        $this->assertCurrentUri();
        $this->assertSuccessAlertContains('Ваш email успешно подтвержден.', $crawler);
    }

    /**
     * Подтверждение регистрации по несуществующему токену
     */
    public function testNotExistedToken(): void
    {
        $crawler = $this->get(self::BASE_URL . '/123', true);

        $this->assertResponseIsSuccessful();

        $this->assertCurrentUri('signup');
        $this->assertDangerAlertContains('Неизвестный или уже подтвержденный токен.', $crawler);
    }

    /**
     * Регистрация уже подтверждена
     */
    public function testAlreadyConfirmed(): void
    {
        $this->loadFixtures([ConfirmFixture::class]);

        $crawler = $this->get(self::BASE_URL . '/token', true);

        $this->assertResponseIsSuccessful();

        $this->assertCurrentUri('signup');
        $this->assertDangerAlertContains('Неизвестный или уже подтвержденный токен.', $crawler);
    }
}
