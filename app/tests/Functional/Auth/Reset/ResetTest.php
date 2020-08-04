<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Reset;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class ResetTest extends DbWebTestCase
{
    private const BASE_URL = '/reset';

    /**
     * Открытие формы с несуществующим токеном
     */
    public function testNotExistedToken()
    {
        $crawler = $this->get(self::BASE_URL . '/123456', true);
        $this->assertResponseIsSuccessful();

        $this->assertCurrentUri();

        $this->assertDangerAlertContains('Неправильный или уже подтвержденный токен.', $crawler);
    }

    /**
     * Отображение страницы с формой аутентификации для гостей с токеном сброса пароля
     */
    public function testShowFormGuest()
    {
        $crawler = $this->get(self::BASE_URL . '/123');
        $this->assertResponseIsSuccessful();

        $this->assertContains('Новый пароль', $crawler->filter('h1')->text());

        $this->assertInputExists('input[id="form_password"]', $crawler);
    }

    /**
     * Отображение страницы с формой аутентификации для аутентифицированных пользователей с токеном сброса пароля
     */
    public function testShowFormAuth()
    {
        $this->auth();
        $this->get(self::BASE_URL . '/123');

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Установка неправильного пароля
     */
    public function testNotValidPassword()
    {
        $this->get(self::BASE_URL . '/123');

        $crawler = $this->submit($this->getNotValidData());

        $this->assertShortPasswordErrorMessage('#form_password', $crawler);
    }

    /**
     * Попытка подтверждения истекшего токена
     */
    public function testExpiredToken()
    {
        $this->get(self::BASE_URL . '/456');

        $crawler = $this->submit($this->getSuccessData());

        $this->assertDangerAlertContains('Токен сброса пароля уже истек.', $crawler);
    }

    /**
     * Успешный сброс пароля
     */
    public function testSuccess()
    {
        $this->get(self::BASE_URL . '/123');

        $crawler = $this->submit($this->getSuccessData(), true);
        $this->assertCurrentUri();
        $this->assertSuccessAlertContains('Пароль успешно изменен.', $crawler);
    }

    /**
     * @return FormDataDto
     */
    public function getSuccessData(): FormDataDto
    {
        $data = [
            'form[password]' => '123qwe',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getNotValidData(): FormDataDto
    {
        $data = [
            'form[password]' => '123',
        ];

        return new FormDataDto($data);
    }
}
