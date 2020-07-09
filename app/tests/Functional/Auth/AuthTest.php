<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends DbWebTestCase
{
    public function testLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Войти', $crawler->filter('h1')->text());
        $this->assertContains('Facebook', $crawler->filter('body')->text());
        $this->assertCount(1, $crawler->filter('input[id="email"]'));
        $this->assertCount(1, $crawler->filter('input[id="password"]'));
    }
}
