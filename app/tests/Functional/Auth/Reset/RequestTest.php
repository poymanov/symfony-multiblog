<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Reset;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;
use Symfony\Component\HttpFoundation\Response;

class RequestTest extends DbWebTestCase
{
    private const BASE_URL = '/reset';

    /**
     * Отображение страницы с формой аутентификации для гостей
     */
    public function testShowFormGuest()
    {
        $crawler = $this->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();

        $this->assertContains('Сброс пароля', $crawler->filter('h1')->text());

        $this->assertInputExists('input[id="form_email"]', $crawler);
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
     * Заполнен некорректный email
     */
    public function testNotValidEmail()
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getNotValidData());

        $this->assertEmailErrorMessage('#form_email', $crawler);
    }

    /**
     * Заполнен несуществующий email
     */
    public function testNotExistingEmail()
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getNotExistingData());

        $this->assertDangerAlertContains('Пользователь не найден.', $crawler);
    }

    /**
     * Email пользователя не подтверждён
     */
    public function testNotConfirmedEmail()
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getNotConfirmedData());
        $this->assertDangerAlertContains('Пользователь ещё не активен.', $crawler);
    }

    /**
     * Сброс пароля уже запрошен
     */
    public function testAlreadyRequestedReset()
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getAlreadyRequestData());
        $this->assertDangerAlertContains('Сброс пароля уже запрошен.', $crawler);
    }

    /**
     * Успешный запрос смены пароля
     */
    public function testSuccess()
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSuccessData(), true);
        $this->assertCurrentUri();
        $this->assertSuccessAlertContains('Проверьте ваш email.', $crawler);
    }

    /**
     * @return FormDataDto
     */
    public function getSuccessData(): FormDataDto
    {
        $data = [
            'form[email]' => 'user@app.test',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getNotExistingData(): FormDataDto
    {
        $data = [
            'form[email]' => 'not-a-existing@email.test',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getNotConfirmedData(): FormDataDto
    {
        $data = [
            'form[email]' => 'not-confirmed-email@email.test',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getAlreadyRequestData(): FormDataDto
    {
        $data = [
            'form[email]' => 'already-requested@email.test',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getNotValidData(): FormDataDto
    {
        $data = [
            'form[email]' => 'not-a-email',
        ];

        return new FormDataDto($data);
    }
}
