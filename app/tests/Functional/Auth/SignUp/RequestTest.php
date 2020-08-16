<?php

namespace App\Tests\Functional\Auth\SignUp;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;
use Symfony\Component\HttpFoundation\Response;

class RequestTest extends DbWebTestCase
{
    private const BASE_URL = '/signup';

    /**
     * Отображение страницы с формой регистрации
     */
    public function testShowForm(): void
    {
        $crawler = $this->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();

        $this->assertContains('Регистрация', $crawler->filter('h1')->text());
        $this->assertContains('Facebook', $crawler->filter('body')->text());

        $this->assertInputExists('input[id="form_firstName"]', $crawler);
        $this->assertInputExists('input[id="form_lastName"]', $crawler);
        $this->assertInputExists('input[id="form_email"]', $crawler);
        $this->assertInputExists('input[id="form_password"]', $crawler);
    }

    /**
     * Отображение страницы с формой регистрации для аутентифицированных пользователей
     */
    public function testShowFormAuth()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Успешная регистрация
     */
    public function testSuccess(): void
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSuccessData(), true);
        $this->assertSuccessAlertContains('Проверьте ваш email.', $crawler);
        $this->assertIsInDatabase('user_users', [
            'email'      => 'tom-bent@app.test',
            'name_first' => 'Tom',
            'name_last'  => 'Bent',
            'alias'      => 'tom-bent',
        ]);
    }

    /**
     * Заполнены не все регистрационные данные
     */
    public function testNotValid(): void
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getNotValidData());

        $this->assertRequiredErrorMessage('#form_firstName', $crawler);
        $this->assertRequiredErrorMessage('#form_lastName', $crawler);
        $this->assertEmailErrorMessage('#form_email', $crawler);
        $this->assertShortPasswordErrorMessage('#form_password', $crawler);
    }

    /**
     * Email уже существует
     */
    public function testExists(): void
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getExistingData());
        $this->assertDangerAlertContains('Пользователь уже существует.', $crawler);
    }

    /**
     * Alias уже существует
     */
    public function testAliasExists(): void
    {
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getExistingAliasData());
        $this->assertDangerAlertContains('Пользователь с alias "first-last" уже существует.', $crawler);
    }

    /**
     * @return FormDataDto
     */
    public function getSuccessData(): FormDataDto
    {
        $data = [
            'form[firstName]' => 'Tom',
            'form[lastName]'  => 'Bent',
            'form[email]'     => 'tom-bent@app.test',
            'form[password]'  => '123qwe',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getNotValidData(): FormDataDto
    {
        $data = [
            'form[firstName]' => '',
            'form[lastName]'  => '',
            'form[email]'     => 'not-email',
            'form[password]'  => '123',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getExistingData(): FormDataDto
    {
        $data = [
            'form[firstName]' => 'existing',
            'form[lastName]'  => 'user',
            'form[email]'     => 'existing-user@app.test',
            'form[password]'  => '123qwe',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getExistingAliasData(): FormDataDto
    {
        $data = [
            'form[firstName]' => 'First',
            'form[lastName]'  => 'Last',
            'form[email]'     => 'first-last@app.test',
            'form[password]'  => '123qwe',
        ];

        return new FormDataDto($data);
    }
}
