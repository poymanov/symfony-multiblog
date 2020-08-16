<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post\Comment;

use App\DataFixtures\UserFixture;
use App\Model\Post\Entity\Post\Post;
use App\Tests\Fixtures\PostFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Helpers\FormDataDto;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends DbWebTestCase
{
    private const BASE_URL = '/posts/another-published-test/comments/create';

    /**
     * Гостям недоступна кнопка добавления публикации
     */
    public function testCreateButtonGuest()
    {
        $crawler = $this->get('/posts/another-published-test/comments');

        $this->assertEquals(0, $crawler->filter('a[href="' . self::BASE_URL . '"]')->count());
    }

    /**
     * Аутентифицированным пользователям доступна кнопка добавления публикации
     */
    public function testCreateButton()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test/comments');

        $this->assertEquals(1, $crawler->filter('a[href="' . self::BASE_URL . '"]')->count());
    }

    /**
     * Страница добавления нового комментария недоступна гостям
     */
    public function testCreatePageGuest()
    {
        $this->get(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Страница добавления нового комментария недоступна для публикации-черновика
     */
    public function testCreatePageDraft()
    {
        $this->auth();
        $this->get('/posts/test/comments/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Страница добавления нового комментария недоступна для несуществующего поста
     */
    public function testCreatePageNotExistedPost()
    {
        $this->auth();
        $this->get('/posts/test-123/comments/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Страница добавления нового комментария доступна для публикации
     */
    public function testCreatePageSuccess()
    {
        $this->auth();
        $this->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();
    }

    /**
     * Выводится ссылка на публикацию, к которой будет относится новый комментарий
     */
    public function testLinkToPost()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);
        $this->assertEquals(1, $crawler->filter('a[href="/posts/another-published-test"]')->count());
    }

    /**
     * Отображается форма добавления нового комментария
     */
    public function testShowForm()
    {
        $this->auth();
        $crawler = $this->get(self::BASE_URL);

        $this->assertInputExists('textarea[id="comment_text"]', $crawler);
    }

    /**
     * Нельзя добавить комментарий для публикации-черновика
     */
    public function testDraftPost()
    {
        $this->auth();
        $this->client->request('POST', '/posts/test/comments/create', ['text' => 'test']);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Нельзя добавить комментарий для несуществующей публикации
     */
    public function testNotExistedPost()
    {
        $this->auth();
        $this->client->request('POST', '/posts/test-123/comments/create', ['text' => 'test']);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Комментарий успешно добавлен
     */
    public function testSuccess()
    {
        $this->auth();

        $this->get(self::BASE_URL);

        $crawler = $this->submit($this->getSuccessData(), true);
        $this->assertCurrentUri('posts/another-published-test/comments');

        $this->assertSuccessAlertContains('Комментарий добавлен.', $crawler);
        $this->assertIsInDatabase('comment_comments', [
            'entity_type' => Post::class,
            'entity_id' => PostFixture::POST_4_ID,
            'author_id' => UserFixture::USER_1_ID,
            'text' => 'test'
        ]);
    }

    /**
     * Получение данных для успешного запроса
     *
     * @return FormDataDto
     */
    private function getSuccessData(): FormDataDto
    {
        $data = [
            'comment[text]' => 'test',
        ];

        return new FormDataDto($data);
    }
}
