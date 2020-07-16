<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers;

use Symfony\Component\DomCrawler\Crawler;

class UrlTestCaseHelper extends AbstractBaseTestCaseHelper
{
    private const BASE_URL = 'http://localhost/';

    /**
     * Проверка соответствия текущего адреса
     *
     * @param string|null $route
     */
    public function assertCurrentUri(string $route = null): void
    {
        $this->testCase->assertSame(self::BASE_URL . $route, $this->testCase->client->getRequest()->getUri());
    }

    /**
     * GET-запрос на адрес, с возможностью следования за редиректом
     *
     * @param string $uri
     * @param bool   $followRedirect
     *
     * @return Crawler
     */
    public function get(string $uri, bool $followRedirect = false): Crawler
    {
        $crawler = $this->testCase->client->request('GET', $uri);

        if ($followRedirect) {
            $crawler = $this->testCase->client->followRedirect();
        }

        return $crawler;
    }
}
