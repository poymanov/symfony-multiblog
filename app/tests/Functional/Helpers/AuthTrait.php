<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers;

use App\DataFixtures\UserFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

trait AuthTrait
{
    /**
     * Аутентификация пользовательского соединения
     *
     * @param array|null $credentials
     */
    public function auth(?array $credentials = null): void
    {
        if (is_null($credentials)) {
            $credentials = UserFixture::userCredentials();
        }

        /** @var $testCase WebTestCase */
        $testCase = $this;

        $testCase->client->setServerParameters($credentials);
    }
}
