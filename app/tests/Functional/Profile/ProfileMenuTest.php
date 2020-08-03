<?php
declare(strict_types=1);

namespace App\Tests\Functional\Profile;

use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ProfileMenuTest extends DbWebTestCase
{
    private const BASE_URL = '/profile';

    /**
     * Проверка наличия активных заголовков в меню на страницах профиля
     *
     * @dataProvider pagesData
     * @param $url
     * @param $expectedTitle
     */
    public function testPages($url, $expectedTitle)
    {
        $this->auth();
        $crawler = $this->get($url);

        $this->assertContains($expectedTitle, $this->getCurrentActiveItemText($crawler));
    }

    /**
     * Набор адресов и ожидаемых на них заголовков
     *
     * @return array
     */
    public function pagesData(): array
    {
        return [
            [self::BASE_URL, 'Профиль'],
            [self::BASE_URL . '/social', 'Социальные сети'],
            [self::BASE_URL . '/posts', 'Публикации'],
        ];
    }

    /**
     * Получение текста текущего активного пункта меню
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getCurrentActiveItemText(Crawler $crawler): string
    {
        return $crawler->filter('a[class="nav-link bg-info active"]')->text();
    }
}
