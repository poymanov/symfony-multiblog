<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post;

use App\Tests\Functional\DbWebTestCase;

class ListTest extends DbWebTestCase
{
    /**
     * Записи в черновиках не отображаются
     */
    public function testWithoutDrafts()
    {
        $crawler = $this->get('/');

        $this->assertNotContains('Draft Test Title', $this->getBodyText($crawler));
        $this->assertNotContains('Draft Test Preview Text', $this->getBodyText($crawler));
    }

    /**
     * Отображаются опубликованные записи
     */
    public function testPublished()
    {
        $crawler = $this->get('/');

        $this->assertContains('Published Test Title', $this->getBodyText($crawler));
        $this->assertContains('Published Test Preview Text', $this->getBodyText($crawler));

        $this->assertContains('Published Test Title 2 ', $this->getBodyText($crawler));
        $this->assertContains('Published Test Preview Text 2', $this->getBodyText($crawler));

        $this->assertContains('test-first-name test-last-name', $this->getBodyText($crawler));
    }

    /**
     * Отображаются ссылки на детальный просмотр публикации
     */
    public function testDetailsLinks()
    {
        $crawler = $this->get('/?page=2');

        $this->assertEquals(2, $crawler->filter('a[href="/posts/published-test-title"]')->count());
    }

    /**
     * Публикации отображаются в нужном порядке (по убыванию даты публикации)
     */
    public function testPublishedOrder()
    {
        $crawler = $this->get('/?page=2');

        $titles = $crawler->filter('h2.blog-post-title')->each(function ($node) {
            return $node->text();
        });

        self::assertTrue(array_search('Published Test Title 2', $titles) < array_search('Published Test Title', $titles));
    }

    /**
     * В списке публикаций отображаются лайки для публикаций с лайками
     */
    public function testLikesCount()
    {
        $crawler = $this->get('/?page=2');

        $likeNode = $crawler->filterXPath('//*[contains(@class, "likes-count")]');

        $this->assertEquals(1, $likeNode->count());
        $this->assertEquals('1', $likeNode->text());
    }

    /**
     * Публикации отображаются с учетом пагинации
     */
    public function testPagination()
    {
        $crawler = $this->get('/');

        $this->assertEquals(20, $crawler->filter('.blog-post')->count());
        $this->assertEquals(2, $crawler->filter('a[href="/?page=2"]')->count());
    }

    /**
     * В списке публикаций отображается количество комментариев
     */
    public function testCommentsCount()
    {
        $crawler = $this->get('/');

        $commentsNode = $crawler->filterXPath('//*[contains(@class, "comments-count")]');

        $this->assertEquals(1, $commentsNode->count());
        $this->assertEquals('25', $commentsNode->text());
    }

    /**
     * В списке публикаций есть ссылки на профили авторов публикаций
     */
    public function testLinkToUserProfile()
    {
        $crawler = $this->get('/');

        self::assertEquals(1, $crawler->filter('a[href="/users/first-last"]')->count());
    }
}
