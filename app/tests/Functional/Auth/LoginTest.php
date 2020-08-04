<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends DbWebTestCase
{
    private const BASE_URL = '/login';

    /**
     * Отображение страницы с формой аутентификации для гостей
     */
    public function testShowFormGuest()
    {
        $crawler = $this->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();

        $this->assertContains('Войти', $crawler->filter('h1')->text());
        $this->assertContains('Facebook', $crawler->filter('body')->text());

        $this->assertInputExists('input[id="email"]', $crawler);
        $this->assertInputExists('input[id="password"]', $crawler);
        $this->assertInputExists('input[id="_remember_me"]', $crawler);
    }

    /**
     * Отображение страницы с формой аутентификации для аутентифицированных пользователей
     */
    public function testShowFormAuth()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Успешная аутентификация
     */
    public function testSuccess(): void
    {
        $this->get(self::BASE_URL);
        $this->submit($this->getSuccessData(), true);
        $this->assertCurrentUri();
    }

    /**
     * Пользователь не подтвержден
     */
    public function testNotConfirmed(): void
    {
        $this->get(self::BASE_URL);
        $crawler = $this->submit($this->getNotConfirmedData(), true);
        $this->assertCurrentUri('login');
        $this->assertDangerAlertContains('Учетная запись отключена.', $crawler);
    }

    /**
     * Заполнен несуществующий email
     */
    public function testNotValidEmail(): void
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getNotExistedData(), true);

        $this->assertCurrentUri('login');
        $this->assertDangerAlertContains('Имя пользователя не найдено.', $crawler);
    }

    /**
     * Заполнен некорректный email
     */
    public function testNotValidPassword(): void
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getNotValidData(), true);

        $this->assertCurrentUri('login');
        $this->assertDangerAlertContains('Недействительные аутентификационные данные.', $crawler);
    }

    /**
     * Получение данных для успешного запроса
     *
     * @return FormDataDto
     */
    private function getSuccessData(): FormDataDto
    {
        $data = [
            'email'    => 'mail@app.test',
            'password' => '123qwe',
        ];

        return new FormDataDto($data);
    }

    /**
     * Получение данных для запроса по неподтвержденному email
     *
     * @return FormDataDto
     */
    private function getNotConfirmedData(): FormDataDto
    {
        $data = [
            'email'    => 'not-confirmed-confirm@app.test',
            'password' => '123qwe',
        ];

        return new FormDataDto($data);
    }

    /**
     * Получение данных для запроса по несуществующему email
     *
     * @return FormDataDto
     */
    private function getNotExistedData(): FormDataDto
    {
        $data = [
            'email'    => 'not-email',
            'password' => '123qwe',
        ];

        return new FormDataDto($data);
    }

    /**
     * Получение данных для запроса по некорректному email
     *
     * @return FormDataDto
     */
    private function getNotValidData(): FormDataDto
    {
        $data = [
            'email'    => 'mail@app.test',
            'password' => '123',
        ];

        return new FormDataDto($data);
    }
}
