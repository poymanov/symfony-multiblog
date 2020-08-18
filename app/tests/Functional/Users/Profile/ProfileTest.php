<?php

declare(strict_types=1);

namespace App\Tests\Functional\Users\Profile;

use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProfileTest extends DbWebTestCase
{
    /**
     * Открытие профиля несуществующего пользователя
     */
    public function testNotExisted()
    {
        $this->get('/users/not-existed-user');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Открытие профиля неподтвержденного пользователя
     */
    public function testNotConfirmedUser()
    {
        $this->get('/users/test-first-name-6-test-last-name-6');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Открытие профиля пользователя
     */
    public function testUserProfile()
    {
        $this->get('/users/first-last');
        self::assertResponseIsSuccessful();
    }

    /**
     * Отображение фамилии, имени пользователя
     */
    public function testFullname()
    {
        $crawler = $this->get('/users/first-last');
        self::assertContains('First Last', $this->getBodyText($crawler));
    }

    /**
     * Отображение количества лайков, которые поставили публикациям пользователя
     */
    public function testLikesCount()
    {
        $crawler = $this->get('/users/test-first-name-test-last-name');

        $likesNode = $crawler->filter('.likes-count');

        $this->assertEquals(1, $likesNode->count());
        $this->assertEquals('1', $likesNode->text());
    }
    /**
     * Отображение количества комментариев, которые оставили публикациям пользователя
     */
    public function testCommentsCount()
    {
        $crawler = $this->get('/users/test-first-name-2-test-last-name-2');

        $likesNode = $crawler->filter('.comments-count');

        $this->assertEquals(1, $likesNode->count());
        $this->assertEquals('25', $likesNode->text());
    }

    /**
     * Вывод общего количества публикаций
     */
    public function testPostsCounter()
    {
        $crawler = $this->get('/users/test-first-name-14-test-last-name-14');

        self::assertContains('25', $crawler->filter('.posts-title')->text());
    }

    /**
     * Для пользователя выводится несколько последних публикаций
     */
    public function testLastPosts()
    {
        $crawler = $this->get('/users/test-first-name-14-test-last-name-14');

        $this->assertEquals(5, $crawler->filter('.blog-post')->count());
    }

    /**
     * Публикации отображаются в нужном порядке (по убыванию даты публикации)
     */
    public function testLastPostsOrder()
    {
        $crawler = $this->get('/users/test-first-name-14-test-last-name-14');

        $titles = $crawler->filter('h3.blog-post-title-profile')->each(function ($node) {
            return $node->text();
        });

        self::assertTrue(array_search('Post #24', $titles) < array_search('Post #23', $titles));
    }

    /**
     * Вывод ссылки на страницу всех публикаций
     */
    public function testAllPostsLink()
    {
        $crawler = $this->get('/users/test-first-name-14-test-last-name-14');

        self::assertEquals(1, $crawler->filter('a[href="/users/test-first-name-14-test-last-name-14/posts"]')->count());
    }

    /**
     * Ссылка на страницу всех публикаций отсутствует для пользователей с небольшим количеством публикаций
     */
    public function testWithoutAllPostsLink()
    {
        $crawler = $this->get('/users/first-last');

        self::assertEquals(0, $crawler->filter('a[href="/users/first-last/posts"]')->count());
    }

    /**
     * Публикации содержат предварительный текст
     */
    public function testLastPostsContainsPreviewText()
    {
        $crawler = $this->get('/users/test-first-name-test-last-name');

        self::assertContains('Published Test Preview Text 2', $this->getBodyText($crawler));
    }

    /**
     * Публикации содержат количество лайков
     */
    public function testLastPostsContainsLikesCount()
    {
        $crawler = $this->get('/users/test-first-name-test-last-name');

        $this->assertEquals('1', $crawler->filterXPath('//*[contains(@class, "post-likes-count")]')->text());
    }

    /**
     * Публикации содержат количество комментариев
     */
    public function testPostsContainsCommentsCount()
    {
        $crawler = $this->get('/users/test-first-name-2-test-last-name-2');

        $this->assertEquals('25', $crawler->filterXPath('//*[contains(@class, "post-comments-count")]')->text());
    }
}
