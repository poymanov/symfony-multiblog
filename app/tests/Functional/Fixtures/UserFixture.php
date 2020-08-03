<?php

declare(strict_types=1);

namespace App\Tests\Functional\Fixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Name;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const REFERENCE_USER = 'test_user';

    public function load(ObjectManager $manager)
    {
        $confirmedUser = (new UserBuilder())
            ->viaEmail(new Email('test-user@app.test'))
            ->withName(new Name('test-first-name', 'test-last-name'))
            ->confirmed()
            ->build();

        $manager->persist($confirmedUser);
        $this->setReference(self::REFERENCE_USER, $confirmedUser);
        $manager->flush();
    }
}
