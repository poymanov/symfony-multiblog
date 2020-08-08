<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\ResetTokenizer;
use App\Tests\Builder\User\UserBuilder;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const REFERENCE_USER = 'test_user';

    public const REFERENCE_USER_2 = 'test_user_2';

    private ObjectManager $manager;

    private PasswordHasher $hasher;

    private ResetTokenizer $tokenizer;

    /**
     * @param PasswordHasher $hasher
     * @param ResetTokenizer $tokenizer
     */
    public function __construct(PasswordHasher $hasher, ResetTokenizer $tokenizer)
    {
        $this->hasher    = $hasher;
        $this->tokenizer = $tokenizer;
    }

    public function load(ObjectManager $manager)
    {
        $hash = $this->hasher->hash('123qwe');

        $this->manager = $manager;

        $confirmedUser = $this->getConfirmedUser()
            ->viaEmail(new Email('test-user@app.test'), $hash)
            ->withName(new Name('test-first-name', 'test-last-name'))
            ->build();

        $this->setReference(self::REFERENCE_USER, $confirmedUser);
        $this->create($confirmedUser);

        $confirmedUser = $this->getConfirmedUser()
            ->viaEmail(new Email('test-user-2@app.test'))
            ->withName(new Name('test-first-name-2', 'test-last-name-2'))
            ->build();

        $this->setReference(self::REFERENCE_USER_2, $confirmedUser);
        $this->create($confirmedUser);

        $invalidToken = $this->getConfirmedUser()
            ->viaEmail(new Email('invalid-new-email-token-user@app.test'), $hash)
            ->withNewEmail(new Email('test@test.ru'), '123')
            ->build();

        $this->create($invalidToken);

        $networkUser = $this->getConfirmedUser()
            ->viaEmail(new Email('user-with-network@app.test'), $hash)
            ->build();

        $networkUser->attachNetwork('facebook', '0001');

        $this->create($networkUser);

        $confirmed = $this->getConfirmedUser()
            ->viaEmail(null, $hash)
            ->build();

        $this->create($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-login@app.test'), $hash)
            ->build();

        $this->create($notConfirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-email@email.test'))
            ->build();

        $this->create($notConfirmed);

        $alreadyRequested = $this->getConfirmedUser()
            ->viaEmail(new Email('already-requested@email.test'))
            ->withResetToken($this->tokenizer->generate())
            ->build();

        $this->create($alreadyRequested);

        $resetToken   = new ResetToken('123', (new DateTimeImmutable())->add(new DateInterval('PT1H')));
        $expiredToken = new ResetToken('456', new DateTimeImmutable());

        $requested = $this->getConfirmedUser()
            ->viaEmail(new Email('request-reset-token@email.test'))
            ->withResetToken($resetToken)
            ->build();

        $this->create($requested);

        $expired = $this->getConfirmedUser()
            ->viaEmail(new Email('expired-token@email.test'))
            ->withResetToken($expiredToken)
            ->build();

        $this->create($expired);

        $existing = $this->getConfirmedUser()
            ->viaEmail(new Email('existing-user@app.test'))
            ->build();

        $this->create($existing);

        $confirmed = $this->getConfirmedUser()
            ->viaEmail(new Email('confirmed@app.test'), null, 'confirmed-token')
            ->build();

        $this->create($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-confirm@app.test'), null, 'not-confirmed-token')
            ->build();

        $this->create($notConfirmed);
    }

    /**
     * @return string[]
     */
    public static function testUserCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'test-user@app.test',
            'PHP_AUTH_PW'   => '123qwe',
        ];
    }

    /**
     * @return array
     */
    public static function invalidTokenUserCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'invalid-new-email-token-user@app.test',
            'PHP_AUTH_PW'   => '123qwe',
        ];
    }

    /**
     * @return array
     */
    public static function networkUserCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'user-with-network@app.test',
            'PHP_AUTH_PW'   => '123qwe',
        ];
    }

    /**
     * @param User $user
     */
    private function create(User $user): void
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @return UserBuilder
     */
    private function getConfirmedUser(): UserBuilder
    {
        return (new UserBuilder())->confirmed();
    }
}
