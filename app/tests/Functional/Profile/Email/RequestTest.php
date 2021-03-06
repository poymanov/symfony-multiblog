<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Email;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;

class RequestTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/email';

    /**
     * Отображение страницы с формой изменения имени для гостей
     */
    public function testShowForm(): void
    {
        $this->get(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Отображение страницы с формой изменения имени для аутентифицированных пользователей
     */
    public function testShowFormAuth()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertContains('Изменить email', $crawler->filter('h1')->text());
        $this->assertInputExists('input[id="form_email"]', $crawler);
    }

    /**
     * Отправка формы с пустыми значениями
     */
    public function testEmpty()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getEmptyData());

        $this->assertRequiredErrorMessage('#form_email', $crawler);
    }

    /**
     * Указан некорректный email
     */
    public function testNotValid(): void
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getNotValidData());

        $this->assertEmailErrorMessage('#form_email', $crawler);
    }

    /**
     * Email уже существует
     */
    public function testExists(): void
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getExistingData());

        $this->assertDangerAlertContains('Email уже используется.', $crawler);
    }

    /**
     * Успешный запрос на изменение email
     */
    public function testSuccess(): void
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSuccessData(), true);

        $this->assertCurrentUri('profile');

        $this->assertSuccessAlertContains('Проверьте ваш email.', $crawler);

        $this->assertIsInDatabase('user_users', [
            'email' => 'user@app.test',
            'new_email' => 'user2@app.test',
        ]);

        $this->assertIsNotInDatabase('user_users', [
            'email' => 'user@app.test',
            'new_email' => 'user2@app.test',
            'new_email_token' => null,
        ]);
    }

    /**
     * @return FormDataDto
     */
    public function getEmptyData(): FormDataDto
    {
        $data = [
            'form[email]' => '',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getSuccessData(): FormDataDto
    {
        $data = [
            'form[email]' => 'user2@app.test',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getNotValidData(): FormDataDto
    {
        $data = [
            'form[email]' => 'not-email',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getExistingData(): FormDataDto
    {
        $data = [
            'form[email]' => 'user@app.test',
        ];

        return new FormDataDto($data);
    }
}
