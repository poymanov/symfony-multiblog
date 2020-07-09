<?php

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\DbWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SignUpTest extends DbWebTestCase
{
    public function testSignUp()
    {
        $crawler = $this->client->request('GET', '/signup');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Регистрация', $crawler->filter('h1')->text());
        $this->assertContains('Facebook', $crawler->filter('body')->text());
        $this->assertCount(1, $crawler->filter('input[id="lastName"]'));
        $this->assertCount(1, $crawler->filter('input[id="firstName"]'));
        $this->assertCount(1, $crawler->filter('input[id="email"]'));
        $this->assertCount(1, $crawler->filter('input[id="password"]'));
    }
}
