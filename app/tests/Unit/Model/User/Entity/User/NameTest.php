<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Model\User\Entity\User\Name;
use App\Tests\Builder\User\UserBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $firstName = 'New First';
        $lastName  = 'New Last';

        $name = new Name($firstName, $lastName);
        $user = (new UserBuilder())->viaEmail()->build();

        $user->changeName($name);
        self::assertEquals($firstName, $user->getName()->getFirst());
        self::assertEquals($lastName, $user->getName()->getLast());
    }

    /**
     * @throws Exception
     */
    public function testExistedAlias(): void
    {
        $user = (new UserBuilder())->withAlias('old-alias')->viaEmail()->build();

        $this->expectExceptionMessage('Alias пользователя остался без изменений.');
        $user->changeAlias('old-alias');
    }

    /**
     * @throws Exception
     */
    public function testSuccessAlias(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->changeAlias($alias = 'new-alias');

        self::assertEquals($alias, $user->getAlias());
    }
}
