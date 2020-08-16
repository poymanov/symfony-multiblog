<?php

declare(strict_types=1);


namespace App\Tests\Functional\Post\Comment;

use App\Tests\Fixtures\CommentFixture;
use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteTest extends DbWebTestCase
{
    private const BASE_URL = '/posts/comments/' . CommentFixture::COMMENT_1_ID . '/delete';

    /**
     * Гости не могут удалять комментарии
     */
    public function testGuest()
    {
        $this->delete(self::BASE_URL, true);
        $this->assertCurrentUri('login');
    }

    /**
     * Допускаются только delete-запросы
     */
    public function testRequestMethod()
    {
        $this->auth();

        $this->get(self::BASE_URL);
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->post(self::BASE_URL);
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Нельзя удалять несуществующий комментарий
     */
    public function testNotExisted()
    {
        $this->auth();

        $this->get('/posts/comments/123/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Нельзя удалять комментарий к несуществующему посту
     */
    public function testNotExistedPost()
    {
        $this->auth();

        $this->delete('/posts/comments/' . CommentFixture::COMMENT_5_ID . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Нельзя удалять чужой комментарий
     */
    public function testSomeoneElseComment()
    {
        $this->auth();

        $this->delete('/posts/comments/' . CommentFixture::COMMENT_2_ID . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Нельзя удалять комментарий к посту-черновику
     */
    public function testPostDraft()
    {
        $this->auth();

        $this->delete('/posts/comments/' . CommentFixture::COMMENT_4_ID . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Нельзя удалять комментарий, если с момента его публикации прошло более 24 часов
     */
    public function testExpiredCreatedAt()
    {
        $this->auth();

        $crawler = $this->delete('/posts/comments/' . CommentFixture::COMMENT_3_ID . '/delete', true);
        $this->assertCurrentUri('posts/another-published-test/comments');

        $this->assertDangerAlertContains('Удаление запрещено. С момента создания комментария прошло более 24 часов.', $crawler);
    }

    /**
     * Успешное удаление комментария
     */
    public function testSuccess()
    {
        $this->auth();

        $crawler = $this->delete(self::BASE_URL, true);
        $this->assertCurrentUri('posts/another-published-test/comments');

        $this->assertSuccessAlertContains('Комментарий удален.', $crawler);
        $this->assertIsNotInDatabase('comment_comments', [
            'id' => CommentFixture::COMMENT_1_ID
        ]);
    }

    /**
     * Гости не видят форму удаления комментариев
     */
    public function testGuestDeleteButton()
    {
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(0, $crawler->filter('form[action="' . self::BASE_URL . '"]')->count());
    }

    /**
     * Пользователи не видят форму удаления чужих комментариев
     */
    public function testSomeoneElseDeleteButton()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(0, $crawler->filter('form[action="/posts/another-published-test/comments/' . CommentFixture::COMMENT_2_ID . '/delete"]')->count());
    }

    /**
     * Пользователи не видят форму удаления своих комментариев, если с момента их создания прошло более суток
     */
    public function testExpiredCreatedAtDeleteButton()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(0, $crawler->filter('form[action="/posts/another-published-test/comments/' . CommentFixture::COMMENT_3_ID . '/delete"]')->count());
    }

    /**
     * Пользователи видят форму удаления своих комментариев, если с момента их создания прошло не более суток
     */
    public function testSuccessDeleteButton()
    {
        $this->auth();
        $crawler = $this->get('/posts/another-published-test/comments');
        self::assertEquals(1, $crawler->filter('form[action="' . self::BASE_URL . '"]')->count());
    }
}
