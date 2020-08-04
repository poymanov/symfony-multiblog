<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\SignUp;

use App\Tests\Functional\DbWebTestCase;

class ConfirmTest extends DbWebTestCase
{
    private const BASE_URL = '/signup';

    /**
     * Успешное подтверждение регистрации
     */
    public function testSuccess(): void
    {
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
        $crawler = $this->get(self::BASE_URL . '/confirmed-token', true);

        $this->assertResponseIsSuccessful();

        $this->assertCurrentUri('signup');
        $this->assertDangerAlertContains('Неизвестный или уже подтвержденный токен.', $crawler);
    }
}
