<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
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

        self::assertEquals($email, $user->getNewEmail());
        self::assertEquals($token, $user->getNewEmailToken());
    }

    /**
     * @throws Exception
     */
    public function testSame(): void
    {
        $user = (new UserBuilder())
            ->viaEmail($email = new Email('new@app.test'))
            ->confirmed()->build();

        $this->expectExceptionMessage('Новый email совпадает с текущим.');
        $user->requestEmailChanging($email, $token = 'token');
    }

    /**
     * @throws Exception
     */
    public function testNotConfirmed(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectExceptionMessage('Пользователь ещё не активен.');
        $user->requestEmailChanging(new Email('new@app.test'), 'token');
    }
}
