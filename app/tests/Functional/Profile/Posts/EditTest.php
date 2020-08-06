<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Posts;

use App\Model\Post\Entity\Post\Id;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Fixtures\PostFixture;
use App\Tests\Functional\Helpers\FormDataDto;
use Exception;

class EditTest extends DbWebTestCase
{
    private const BASE_URL = '/profile/posts/edit/';
    private const POST_1_URL = self::BASE_URL . PostFixture::POST_1_ID;

    /**
     * Для гостей страница недоступна
     */
    public function testShowGuest(): void
    {
        $this->get(self::POST_1_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Попытка редактирования несуществующего поста
     *
     * @throws Exception
     */
    public function testEditNotExistedPost(): void
    {
        $this->auth();
        $this->get(self::BASE_URL . Id::next());

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Попытка редактирования чужой публикации
     *
     * @throws Exception
     */
    public function testEditAnotherUserPost(): void
    {
        $this->auth();
        $this->get(self::BASE_URL . PostFixture::POST_2_ID);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Отображение кнопки "Назад", для перехода в список всех публикаций
     */
    public function testBackLink()
    {
        $this->auth();
        $crawler = $this->get(self::POST_1_URL);

        $this->assertEquals(1, $crawler->filter('a[href="/profile/posts"]')->count());
    }

    /**
     * Отображение формы с заполненными значениями публикациями
     */
    public function testShowFormWithCurrentValues()
    {
        $this->auth();
        $crawler = $this->get(self::POST_1_URL);

        $this->assertInputExists('input[id="form_title"]', $crawler);
        $this->assertInputExists('textarea[id="form_previewText"]', $crawler);
        $this->assertInputExists('textarea[id="form_text"]', $crawler);

        $this->assertEquals(1, $crawler->filter('input[value="Test"]')->count());
        $this->assertContains('Preview Text', $crawler->filter('textarea[id="form_previewText"]')->text());
        $this->assertContains('Text', $crawler->filter('textarea[id="form_text"]')->text());
    }

    /**
     * Отправка формы с пустыми значениями
     */
    public function testEmpty()
    {
        $this->auth();
        $this->get(self::POST_1_URL);

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
        $this->get(self::POST_1_URL);

        $crawler = $this->submit($this->getExistedData());

        $this->assertCurrentUri('profile/posts/edit/' . PostFixture::POST_1_ID);
        $this->assertDangerAlertContains('Публикация с таким alias уже существует.', $crawler);
    }

    /**
     * Успешное редактирование публикации без каких-либо изменений
     */
    public function testSuccessWithNoChanges()
    {
        $this->auth();
        $this->get(self::POST_1_URL);

        $crawler = $this->submit($this->getSuccessWithNoChangesData(), true);

        $this->assertCurrentUri('profile/posts');
        $this->assertSuccessAlertContains('Публикация изменена.', $crawler);
    }

    /**
     * Успешное редактирование публикации
     */
    public function testSuccess()
    {
        $this->auth();
        $this->get(self::POST_1_URL);

        $crawler = $this->submit($this->getSuccessData(), true);

        $this->assertCurrentUri('profile/posts');
        $this->assertSuccessAlertContains('Публикация изменена.', $crawler);
    }

    /**
     * На странице редактирования черновика есть кнопка его публикации
     */
    public function testDraftHavePublishButton()
    {
        $this->auth();
        $crawler = $this->get(self::POST_1_URL);

        $this->assertEquals(1, $crawler->filter('form[action="/profile/posts/publish/' . PostFixture::POST_1_ID . '"]')->count());
        $this->assertContains('Опубликовать', $crawler->filterXPath('//button[@type="submit"]')->text());
    }

    /**
     * На странице редактирования публикации есть кнопка его перевода в черновики
     */
    public function testPublishedHaveDraftButton()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL . PostFixture::POST_3_ID);

        $this->assertEquals(1, $crawler->filter('form[action="/profile/posts/draft/' . PostFixture::POST_3_ID . '"]')->count());
        $this->assertContains('В черновик', $crawler->filterXPath('//button[@type="submit"]')->text());
    }

    /**
     * На странице редактирования опубликованной записи нет кнопки его публикации
     */
    public function testPublishedDontHavePublishButton()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL . PostFixture::POST_3_ID);

        $this->assertEquals(0, $crawler->filter('form[action="/profile/posts/publish/' . PostFixture::POST_3_ID . '"]')->count());
        $this->assertNotContains('Опубликовать', $crawler->filterXPath('//button[@type="submit"]')->text());
    }

    /**
     * На странице редактирования черновика нет кнопки его перевода в черновики
     */
    public function testDraftDontHaveDraftButton()
    {
        $this->auth();
        $crawler = $this->get(self::POST_1_URL);

        $this->assertEquals(0, $crawler->filter('form[action="/profile/posts/draft/' . PostFixture::POST_1_ID . '"]')->count());
        $this->assertNotContains('В черновик', $crawler->filterXPath('//button[@type="submit"]')->text());
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
            'form[title]'       => 'Another Test',
            'form[previewText]' => 'Another Test',
            'form[text]'        => 'Another Test',
        ];

        return new FormDataDto($data);
    }

    /**
     * @return FormDataDto
     */
    public function getSuccessWithNoChangesData(): FormDataDto
    {
        $data = [
            'form[title]'       => 'Test',
            'form[previewText]' => 'Preview Text',
            'form[text]'        => 'Text',
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
