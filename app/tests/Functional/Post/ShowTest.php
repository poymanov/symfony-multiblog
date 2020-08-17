<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post;

use App\Tests\Fixtures\PostFixture;
use App\Tests\Functional\DbWebTestCase;

class ShowTest extends DbWebTestCase
{
    private const POST_DETAIL_BASE_URL = '/posts/';

    /**
     * Несуществующий пост не открывается
     */
    public function testNotExistedPost()
    {
        $this->get(self::POST_DETAIL_BASE_URL . '123');

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Запись из черновиков не открывается
     */
    public function testDraftPost()
    {
        $this->get(self::POST_DETAIL_BASE_URL . 'test');

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Запись не открывается по uuid
     */
    public function testGetByPostUuid()
    {
        $this->get(self::POST_DETAIL_BASE_URL . PostFixture::POST_3_ID);

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Просмотр страницы детальной информации по публикации
     */
    public function testPostDetails()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'published-test-title');

        $this->assertResponseIsSuccessful();

        $this->assertContains('Published Test Title', $this->getBodyText($crawler));
        $this->assertContains('Published Test Preview Text', $this->getBodyText($crawler));
        $this->assertContains('Published Test Text', $this->getBodyText($crawler));
        $this->assertContains('test-first-name test-last-name', $this->getBodyText($crawler));
    }

    /**
     * Отображение количества лайков, если они присутствуют
     */
    public function testLikesCount()
    {
        $crawler = $this->get('/posts/published-test-title');

        $this->assertEquals('1', $crawler->filterXPath('//*[contains(@class, "likes-count")]')->text());
    }

    /**
     * Не отображать лайки, если они отсутствуют
     */
    public function testPostWithoutLikes()
    {
        $crawler = $this->get('/posts/published-test-title-2');

        $this->assertEquals(0, $crawler->filterXPath('//*[contains(@class, "likes-count")]')->count());
    }

    /**
     * Для публикации выводится несколько последних комментариев
     */
    public function testLastComments()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        $this->assertEquals(5, $crawler->filter('.comment')->count());
    }

    /**
     * Комментарии выводятся в нужном порядке (по возрастанию даты создания)
     */
    public function testLastCommentsOrder()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        $texts = $crawler->filter('.comment .card-text')->each(function ($node) {
            return $node->text();
        });

        self::assertTrue(array_search('First Comment', $texts) < array_search('Second Comment', $texts));
    }

    /**
     * Вывод общего количества комментариев и ссылки на страницу всех комментариев
     */
    public function testCommentsCounter()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        self::assertContains('5', $crawler->filter('.comments-title')->text());
    }

    /**
     * Вывод ссылки на страницу всех комментариев
     */
    public function testAllCommentsLink()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        self::assertEquals(1, $crawler->filter('a[href="' . self::POST_DETAIL_BASE_URL . 'another-published-test/comments' . '"]')->count());
    }

    /**
     * Если комментариев мало - не выводить ссылку на страницу всех комментариев
     */
    public function testWithoutAllCommentsLink()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'published-test-text-2');

        self::assertEquals(0, $crawler->filter('a[href="' . self::POST_DETAIL_BASE_URL . 'another-published-test/comments' . '"]')->count());
    }

    /**
     * Гости не видят кнопку добавления нового комментария
     */
    public function testGuestAddCommentButton()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        self::assertEquals(0, $crawler->filter('a[href="' . self::POST_DETAIL_BASE_URL . 'another-published-test/comments/create' . '"]')->count());
    }

    /**
     * Аутентифицированные пользователи видят кнопку добавления нового комментария
     */
    public function testAddCommentButton()
    {
        $this->auth();
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        self::assertEquals(1, $crawler->filter('a[href="' . self::POST_DETAIL_BASE_URL . 'another-published-test/comments/create' . '"]')->count());
    }

    /**
     * Есть ссылка на профиль автора публикации
     */
    public function testLinkToUserProfile()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        self::assertEquals(1, $crawler->filter('a[href="/users/test-first-name-2-test-last-name-2"]')->count());
    }

    /**
     * Есть ссылка на профиль автора публикации
     */
    public function testLinkToCommentAuthorProfile()
    {
        $crawler = $this->get(self::POST_DETAIL_BASE_URL . 'another-published-test');

        self::assertEquals(1, $crawler->filter('a[href="/users/test-first-name-test-last-name"]')->count());
    }
}
