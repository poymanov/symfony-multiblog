<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;

class HomeTest extends DbWebTestCase
{
    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Блоги обо всём', $crawler->filter('h1')->text());
    }
}
