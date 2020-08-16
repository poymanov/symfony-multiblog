<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;
use Faker;

class ChangeNameTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/name';

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

        $this->assertContains('Изменить имя', $crawler->filter('h1')->text());

        $this->assertInputExists('input[id="form_first"]', $crawler);
        $this->assertInputExists('input[id="form_last"]', $crawler);
    }

    /**
     * Отправка формы с пустыми значениями
     */
    public function testEmpty()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getEmptyData());

        $this->assertRequiredErrorMessage('#form_first', $crawler);
        $this->assertRequiredErrorMessage('#form_last', $crawler);
    }

    /**
     * Отправка формы с значениями больше допустимой длины
     */
    public function testLong()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getLongData());

        $this->assertTooLongErrorMessage('#form_first', 255, $crawler);
        $this->assertTooLongErrorMessage('#form_last', 255, $crawler);
    }

    /**
     * Изменение имени и создание alias, который уже существует
     */
    public function testExistedAlias()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getExistedAliasData());
        $this->assertDangerAlertContains('Пользователь с alias "first-last" уже существует.', $crawler);
    }

    /**
     * Успешная изменение данных
     */
    public function testSuccess(): void
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSuccessData(), true);
        $this->assertSuccessAlertContains('Имя изменено.', $crawler);

        $this->assertIsInDatabase('user_users', [
            'id'         => UserFixture::USER_1_ID,
            'name_first' => 'New First Name',
            'name_last'  => 'New Last Name',
            'alias'      => 'new-first-name-new-last-name',
        ]);
    }

    /**
     * @return FormDataDto
     */
    public function getEmptyData(): FormDataDto
    {
        $data = [
            'form[first]' => '',
            'form[last]'  => '',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getLongData(): FormDataDto
    {
        $faker = Faker\Factory::create();

        $longData = $faker->paragraph(10);

        $data = [
            'form[first]' => $longData,
            'form[last]'  => $longData,
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getExistedAliasData(): FormDataDto
    {
        $faker = Faker\Factory::create();

        $data = [
            'form[first]' => 'First',
            'form[last]'  => 'Last',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getSuccessData(): FormDataDto
    {
        $faker = Faker\Factory::create();

        $data = [
            'form[first]' => 'New First Name',
            'form[last]'  => 'New Last Name',
        ];

        return new FormDataDto($data);
    }
}
