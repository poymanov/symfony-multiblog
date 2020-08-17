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

        $likesNode = $crawler->filterXPath('//*[contains(@class, "likes-count")]');

        $this->assertEquals(1, $likesNode->count());
        $this->assertEquals('1', $likesNode->text());
    }
    /**
     * Отображение количества комментариев, которые оставили публикациям пользователя
     */
    public function testCommentsCount()
    {
        $crawler = $this->get('/users/test-first-name-2-test-last-name-2');

        $likesNode = $crawler->filterXPath('//*[contains(@class, "comments-count")]');

        $this->assertEquals(1, $likesNode->count());
        $this->assertEquals('25', $likesNode->text());
    }
}
