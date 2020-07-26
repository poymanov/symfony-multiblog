<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Network;

use App\Model\User\Entity\User\Email;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class NetworkFixture extends Fixture
{
    private PasswordHasher $hasher;

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

        $networkUser = (new UserBuilder())
            ->viaEmail(new Email('user-with-network@app.test'), $hash)
            ->confirmed()
            ->build();

        $networkUser->attachNetwork('facebook', '0001');

        $manager->persist($networkUser);

        $manager->flush();
    }

    /**
     * @return array
     */
    public static function userCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'user-with-network@app.test',
            'PHP_AUTH_PW' => '123qwe',
        ];
    }
}
