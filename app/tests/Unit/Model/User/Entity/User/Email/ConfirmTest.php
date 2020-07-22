<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            $email = new Email('new@app.test'),
            $token = 'token'
        );

        $user->confirmEmailChanging($token);

        self::assertEquals($email, $user->getEmail());
        self::assertNull($user->getNewEmailToken());
        self::assertNull($user->getNewEmail());
    }

    /**
     * @throws Exception
     */
    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $this->expectExceptionMessage('Изменение email не было запрошено.');
        $user->confirmEmailChanging('token');
    }

    /**
     * @throws Exception
     */
    public function testIncorrect(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            $email = new Email('new@app.test'),
            $token = 'token'
        );

        $this->expectExceptionMessage('Неверный токен изменения пароля.');
        $user->confirmEmailChanging('incorrect-token');
    }
}
