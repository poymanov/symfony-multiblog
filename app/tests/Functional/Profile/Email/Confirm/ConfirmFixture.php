<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Email\Confirm;

use App\Model\User\Entity\User\Email;
use App\Model\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ConfirmFixture extends Fixture
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

        $invalidToken = (new UserBuilder())
            ->viaEmail(new Email('invalid-new-email-token-user@app.test'), $hash)
            ->confirmed()
            ->withNewEmail(new Email('test@test.ru'), '123')
            ->build();

        $manager->persist($invalidToken);

        $manager->flush();
    }

    /**
     * @return array
     */
    public static function userCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'invalid-new-email-token-user@app.test',
            'PHP_AUTH_PW' => '123qwe',
        ];
    }
}
