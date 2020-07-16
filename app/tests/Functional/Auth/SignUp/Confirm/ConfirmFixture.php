<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\SignUp\Confirm;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ConfirmFixture extends Fixture
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $confirmed = (new UserBuilder())
            ->viaEmail()
            ->confirmed()
            ->build();

        $manager->persist($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed@app.test'), null, 'not-confirmed-token')
            ->build();

        $manager->persist($notConfirmed);

        $manager->flush();
    }
}
