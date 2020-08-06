<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Fixtures\PostFixture;

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
}
