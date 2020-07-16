<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->block();

        $user->activate();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isBlocked());
    }

    /**
     * @throws Exception
     */
    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->activate();

        $this->expectExceptionMessage('Пользователь уже активирован.');
        $user->activate();
    }
}
