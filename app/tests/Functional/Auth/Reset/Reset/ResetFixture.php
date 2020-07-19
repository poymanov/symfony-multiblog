<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Reset\Reset;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ResetFixture extends Fixture
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $resetToken   = new ResetToken('123', (new DateTimeImmutable())->add(new DateInterval('PT1H')));
        $expiredToken = new ResetToken('456', new DateTimeImmutable());

        $requested = (new UserBuilder())
            ->viaEmail()
            ->confirmed()
            ->withResetToken($resetToken)
            ->build();

        $manager->persist($requested);

        $expired = (new UserBuilder())
            ->viaEmail(new Email('expired-token@email.test'))
            ->confirmed()
            ->withResetToken($expiredToken)
            ->build();

        $manager->persist($expired);

        $manager->flush();
    }
}
