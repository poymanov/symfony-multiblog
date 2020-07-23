<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Login;


use App\Model\User\Entity\User\Email;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class LoginFixture extends Fixture
{
    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * @param PasswordHasher $hasher
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('123qwe');

        $confirmed = (new UserBuilder())
            ->viaEmail(null, $hash)
            ->confirmed()
            ->build();

        $manager->persist($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-login@app.test'), $hash)
            ->build();

        $manager->persist($notConfirmed);

        $manager->flush();
    }
}
