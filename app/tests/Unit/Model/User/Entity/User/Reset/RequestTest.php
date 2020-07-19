<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());
    }

    /**
     * @throws Exception
     */
    public function testAlready(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestPasswordReset($token, $now);
        $this->expectExceptionMessage('Сброс пароля уже запрошен.');
        $user->requestPasswordReset($token, $now);
    }

    /**
     * @throws Exception
     */
    public function testExpired(): void
    {
        $now = new DateTimeImmutable();

        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $token1 = new ResetToken('token', $now->modify('+1 day'));
        $user->requestPasswordReset($token1, $now);

        self::assertEquals($token1, $user->getResetToken());

        $token2 = new ResetToken('token', $now->modify('+3 day'));
        $user->requestPasswordReset($token2, $now->modify('+2 day'));

        self::assertEquals($token2, $user->getResetToken());
    }

    /**
     * @throws Exception
     */
    public function testNotConfirmed(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectExceptionMessage('Пользователь ещё не активен.');
        $user->requestPasswordReset($token, $now);
    }

    /**
     * @throws Exception
     */
    public function testWithoutEmail()
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaNetwork()->build();

        $this->expectExceptionMessage('Email не определен.');
        $user->requestPasswordReset($token, $now);
    }
}
