<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Posts;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;

class CreateTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/posts/create';

    /**
     * Для гостей страница недоступна
     */
    public function testShowForm(): void
    {
        $this->get(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Отправка формы с пустыми значениями
     */
    public function testEmpty()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getEmptyData());

        $this->assertRequiredErrorMessage('#form_title', $crawler);
        $this->assertRequiredErrorMessage('#form_previewText', $crawler);
        $this->assertRequiredErrorMessage('#form_text', $crawler);
    }

    /**
     * Отправка формы с заголовком для формирования alias, который уже есть в БД
     */
    public function testExisted()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getExistedData());

        $this->assertCurrentUri('profile/posts/create');
        $this->assertDangerAlertContains('Публикация с таким alias уже существует.', $crawler);
    }

    /**
     * Успешное создание публикации
     */
    public function testSuccess()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSuccessData(), true);

        $this->assertCurrentUri('profile/posts');
        $this->assertSuccessAlertContains('Новая запись опубликована.', $crawler);
    }

    /**
     * @return FormDataDto
     */
    public function getEmptyData(): FormDataDto
    {
        $data = [
            'form[title]'       => '',
            'form[previewText]' => '',
            'form[text]'        => '',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getExistedData(): FormDataDto
    {
        $data = [
            'form[title]'       => 'Test',
            'form[previewText]' => 'Test',
            'form[text]'        => 'test',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getSuccessData(): FormDataDto
    {
        $data = [
            'form[title]'       => 'Test 2',
            'form[previewText]' => 'Test 2',
            'form[text]'        => 'test 2',
        ];

        return new FormDataDto($data);
    }
}
