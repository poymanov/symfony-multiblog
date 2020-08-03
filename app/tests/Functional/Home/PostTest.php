<?php

declare(strict_types=1);

namespace App\Tests\Functional\Home;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Fixtures\PostFixture;
use Symfony\Component\DomCrawler\Crawler;

class PostTest extends DbWebTestCase
{
    private const POST_DETAIL_BASE_URL = '/posts/';

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
        $crawler = $this->get('/');

        $this->assertEquals(2, $crawler->filter('a[href="/posts/published-test-title"]')->count());
    }

    /**
     * Публикации отображаются в нужном порядке (по убыванию даты публикации)
     */
    public function testPublishedOrder()
    {
        $crawler = $this->get('/');

        $titles = $crawler->filter('h2.blog-post-title')->each(function ($node) {
            return $node->text();
        });

        self::assertTrue(array_search('Published Test Title 2', $titles) < array_search('Published Test Title', $titles));

    }

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
     * Получение содержимого страницы
     *
     * @param Crawler $crawler
     *
     * @return string
     */
    private function getBodyText(Crawler $crawler): string
    {
        return $crawler->filter('body')->text();
    }
}
