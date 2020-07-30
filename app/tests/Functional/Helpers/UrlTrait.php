<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

trait UrlTrait
{
    /**
     * Проверка соответствия текущего адреса
     *
     * @param string|null $route
     */
    public function assertCurrentUri(string $route = null): void
    {
        /** @var $testCase WebTestCase */
        $testCase = $this;

        $baseUrl = 'http://localhost/';

        $testCase->assertSame($baseUrl . $route, $testCase->client->getRequest()->getUri());
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
        return $this->request('GET', $uri, $followRedirect);
    }

    /**
     * PATCH-запрос на адрес, с возможностью следования за редиректом
     *
     * @param string $uri
     * @param bool   $followRedirect
     *
     * @return Crawler
     */
    public function patch(string $uri, bool $followRedirect = false): Crawler
    {
        return $this->request('PATCH', $uri, $followRedirect);
    }

    /**
     * Запрос на адрес с указанием метода запроса
     *
     * @param string $method
     * @param string $uri
     * @param bool   $followRedirect
     */
    private function request(string $method, string $uri, bool $followRedirect = false): Crawler
    {
        /** @var $testCase WebTestCase */
        $testCase = $this;

        $crawler = $testCase->client->request($method, $uri);

        if ($followRedirect) {
            $crawler = $testCase->client->followRedirect();
        }

        return $crawler;
    }


}
