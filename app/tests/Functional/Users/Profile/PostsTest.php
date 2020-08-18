<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users\Profile;

use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostsTest extends DbWebTestCase
{
    private const BASE_URL = '/users/test-first-name-14-test-last-name-14/posts';

    /**
     * Открытие публикаций несуществующего пользователя
     */
    public function testUserNotExisted()
    {
        $this->get('/users/not-existed-user/posts');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Открытие публикаций неподтвержденного пользователя
     */
    public function testUserNotConfirmedUser()
    {
        $this->get('/users/test-first-name-6-test-last-name-6/posts');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Отображение страницы с публикациями
     */
    public function testPosts()
    {
        $this->get(self::BASE_URL);
        self::assertResponseIsSuccessful();
    }

    /**
     * Отображается ссылка на профиль пользователя, для которого выводятся публикации
     */
    public function testLinkToUser()
    {
        $crawler = $this->get(self::BASE_URL);

        self::assertEquals(1, $crawler->filter('a[href="/users/test-first-name-14-test-last-name-14"]')->count());
    }

    /**
     * Публикации отображаются с учетом пагинации
     */
    public function testPagination()
    {
        $crawler = $this->get(self::BASE_URL);

        $this->assertEquals(20, $crawler->filter('.blog-post')->count());
        $this->assertEquals(2, $crawler->filter('a[href="' . self::BASE_URL . '?page=2"]')->count());
    }

    /**
     * Отображаются только опубликованные публикации
     */
    public function testShowPublished()
    {
        $crawler = $this->get('/users/first-last/posts');

        self::assertNotContains('Preview Text', $this->getBodyText($crawler));
    }

    /**
     * Публикации отображаются в нужном порядке (по убыванию даты публикации)
     */
    public function testOrder()
    {
        $crawler = $this->get(self::BASE_URL);

        $titles = $crawler->filter('h2.blog-post-title')->each(function ($node) {
            return $node->text();
        });

        self::assertTrue(array_search('Post #24', $titles) < array_search('Post #23', $titles));
    }

    /**
     * Публикации содержат предварительный текст
     */
    public function testPostsContainsPreviewText()
    {
        $crawler = $this->get('/users/test-first-name-test-last-name/posts');

        self::assertContains('Published Test Preview Text 2', $this->getBodyText($crawler));
    }

    /**
     * Публикации содержат количество лайков
     */
    public function testPostsContainsLikesCount()
    {
        $crawler = $this->get('/users/test-first-name-test-last-name/posts');

        $this->assertEquals('1', $crawler->filterXPath('//*[contains(@class, "likes-count")]')->text());
    }

    /**
     * Публикации содержат количество комментариев
     */
    public function testPostsContainsCommentsCount()
    {
        $crawler = $this->get('/users/test-first-name-2-test-last-name-2/posts');

        $this->assertEquals('25', $crawler->filterXPath('//*[contains(@class, "comments-count")]')->text());
    }
}
