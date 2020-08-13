<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post\Comment;

use App\Tests\Fixtures\CommentFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;
use Symfony\Component\HttpFoundation\Response;

class EditTest extends DbWebTestCase
{
    private const BASE_URL = '/posts/comments/' . CommentFixture::COMMENT_1_ID . '/edit';

    /**
     * Гостям недоступна страница редактирования комментария
     */
    public function testGuest()
    {
        $this->get(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Страница редактирования несуществующего комментария недоступна
     */
    public function testNotExisted()
    {
        $this->auth();
        $this->get('/posts/comments/123/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Страница редактирования комментария для несуществующей публикации недоступна
     */
    public function testNotExistedPost()
    {
        $this->auth();
        $this->get('/posts/comments/' . CommentFixture::COMMENT_5_ID . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Страница редактирования чужого комментария недоступна
     */
    public function testSomeoneElseComment()
    {
        $this->auth();
        $this->get('/posts/comments/' . CommentFixture::COMMENT_2_ID . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Страница редактирования комментария недоступна, если с момента его создания прошло более 24 часов
     */
    public function testExpiredCreatedAt()
    {
        $this->auth();
        $this->get('/posts/comments/' . CommentFixture::COMMENT_3_ID . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Страница редактирования комментария недоступна, если публикация, для которой он оставлен, является черновиком
     */
    public function testDraft()
    {
        $this->auth();
        $this->get('/posts/comments/' . CommentFixture::COMMENT_4_ID . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Выводится ссылка на публикацию, для которого редактируется комментарий
     */
    public function testLinkToPost()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertEquals(1, $crawler->filter('a[href="/posts/another-published-test"]')->count());
    }

    /**
     * Отображается форма редактирования комментария
     */
    public function testShowForm()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertInputExists('textarea[id="comment_text"]', $crawler);

        self::assertEquals('First Comment', $crawler->filter('textarea[id="comment_text"]')->text());
    }

    /**
     * Редактирование комментария с текстом без изменений
     */
    public function testSameText()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSameData());
        $this->assertDangerAlertContains('Ошибка редактирования. Текст комментария не изменен.', $crawler);
    }

    /**
     * Редактирование комментария с текстом без изменений
     */
    public function testSuccessText()
    {
        $this->auth();
        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSuccessData(), true);
        $this->assertCurrentUri('posts/another-published-test/comments');
        $this->assertSuccessAlertContains('Комментарий изменен.', $crawler);
    }

    /**
     * Гости не видят кнопки редактирования комментариев
     */
    public function testGuestEditButton()
    {
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(0, $crawler->filter('a[href="' . self::BASE_URL . '"]')->count());
    }

    /**
     * Пользователи не видят кнопки редактирования чужих комментариев
     */
    public function testSomeoneElseEditButton()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(0, $crawler->filter('a[href="/posts/another-published-test/comments/' . CommentFixture::COMMENT_2_ID . '/edit"]')->count());
    }

    /**
     * Пользователи не видят кнопки редактирования своих комментариев, если с момента их создания прошло более суток
     */
    public function testExpiredCreatedAtEditButton()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(0, $crawler->filter('a[href="/posts/another-published-test/comments/' . CommentFixture::COMMENT_3_ID . '/edit"]')->count());
    }

    /**
     * Пользователи видят кнопки редактирования своих комментариев, если с момента их создания прошло не более суток
     */
    public function testSuccessEditButton()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(1, $crawler->filter('a[href="' . self::BASE_URL . '"]')->count());
    }

    /**
     * Получение данных для успешного запроса
     *
     * @return FormDataDto
     */
    private function getSuccessData(): FormDataDto
    {
        $data = [
            'comment[text]' => 'test2',
        ];

        return new FormDataDto($data);
    }

    /**
     * Получение данных для запроса с данными без изменений
     *
     * @return FormDataDto
     */
    private function getSameData(): FormDataDto
    {
        $data = [
            'comment[text]' => 'First Comment',
        ];

        return new FormDataDto($data);
    }
}
