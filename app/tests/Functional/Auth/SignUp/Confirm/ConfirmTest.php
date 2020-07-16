<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\SignUp\Confirm;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ConfirmTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/signup';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
        $this->alert = new AlertTestCaseHelper($this);
    }

    /**
     * Успешное подтверждение регистрации
     */
    public function testSuccess(): void
    {
        $this->loadFixtures([ConfirmFixture::class]);

        $crawler = $this->url->get(self::BASE_URL . '/not-confirmed-token', true);
        $this->assertResponseIsSuccessful();

        $this->url->assertCurrentUri();
        $this->alert->assertSuccessAlertContains('Ваш email успешно подтвержден.', $crawler);
    }

    /**
     * Подтверждение регистрации по несуществующему токену
     */
    public function testNotExistedToken(): void
    {
        $crawler = $this->url->get(self::BASE_URL . '/123', true);

        $this->assertResponseIsSuccessful();

        $this->url->assertCurrentUri('signup');
        $this->alert->assertDangerAlertContains('Неизвестный или уже подтвержденный токен.', $crawler);
    }

    /**
     * Регистрация уже подтверждена
     */
    public function testAlreadyConfirmed(): void
    {
        $this->loadFixtures([ConfirmFixture::class]);

        $crawler = $this->url->get(self::BASE_URL . '/token', true);

        $this->assertResponseIsSuccessful();

        $this->url->assertCurrentUri('signup');
        $this->alert->assertDangerAlertContains('Неизвестный или уже подтвержденный токен.', $crawler);
    }
}
