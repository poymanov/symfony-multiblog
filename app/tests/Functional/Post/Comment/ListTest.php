<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post\Comment;

use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ListTest extends DbWebTestCase
{
    private const BASE_URL = '/posts/another-published-test/comments';

    /**
     * Для публикации-черновика не отображается страница с комментариями
     */
    public function testDraftPost()
    {
        $this->get('/posts/test/comments');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Для несуществующей публикации не отображается страница с комментариями
     */
    public function testNotExistedPost()
    {
        $this->get('/posts/test-123/comments');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Для опубликованной публикации отображается страница с комментариями
     */
    public function testSuccess()
    {
        $this->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();
    }

    /**
     * Выводится ссылка на публикацию, к которой относится страница с комментариями
     */
    public function testLinkToPost()
    {
        $crawler = $this->get(self::BASE_URL);
        $this->assertEquals(1, $crawler->filter('a[href="/posts/another-published-test"]')->count());
    }

    /**
     * Вывод общего количества комментариев
     */
    public function testAllCommentsCount()
    {
        $crawler = $this->get(self::BASE_URL);

        $commentNode = $crawler->filterXPath('//*[contains(@class, "comment-count")]');

        $this->assertEquals(1, $commentNode->count());
        $this->assertEquals('25', $commentNode->text());
    }
    
    /**
     * Вывод комментариев с учетом пагинации
     */
    public function testPagination()
    {
        $crawler = $this->get(self::BASE_URL);

        $this->assertEquals(20, $crawler->filter('.comment')->count());
        $this->assertEquals(2, $crawler->filter('a[href="' . self::BASE_URL . '?page=2"]')->count());
    }

    /**
     * Публикации отображаются в нужном порядке (по возрастанию даты создания)
     */
    public function testOrder()
    {
        $crawler = $this->get(self::BASE_URL);

        $texts = $crawler->filter('.card-text')->each(function ($node) {
            return $node->text();
        });

        self::assertTrue(array_search('First Comment', $texts) < array_search('Second Comment', $texts));
    }

    /**
     * Комментарий содержит имя автора, текст комментария и дату его публикации
     */
    public function testContent()
    {
        $crawler = $this->get(self::BASE_URL . '?page=2');

        $this->assertContains('First Last', $crawler->filter('body')->text());
        $this->assertContains('Last Comment', $crawler->filter('body')->text());
        $this->assertContains('10-08-2100 22:55', $crawler->filter('body')->text());
    }

    /**
     * Комментарий содержит дату редактирования
     */
    public function testEditDate()
    {
        $crawler = $this->get(self::BASE_URL);
        $this->assertContains('10-08-2099 21:55', $crawler->filter('body')->text());
    }
}
