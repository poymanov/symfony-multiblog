<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Login;


use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class LoginFixture extends Fixture
{
    public const PASSWORD_HASH = '$argon2i$v=19$m=65536,t=4,p=1$MkVWWXpiakVqSElURE91aA$Sp1vPn0yRpBvGkZNodBs7deUJxlq/1HqRO6tskG0orE';//123qwe

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $confirmed = (new UserBuilder())
            ->viaEmail(null, self::PASSWORD_HASH)
            ->confirmed()
            ->build();

        $manager->persist($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed@app.test'), self::PASSWORD_HASH)
            ->build();

        $manager->persist($notConfirmed);

        $manager->flush();
    }
}
