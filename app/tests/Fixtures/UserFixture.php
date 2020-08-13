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

    public const REFERENCE_USER_3 = 'test_user_3';

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

        $confirmedUser = $this->getConfirmedUser()
            ->viaEmail(new Email('test-user@app.test'), $hash)
            ->withName(new Name('test-first-name', 'test-last-name'))
            ->build();

        $this->setReference(self::REFERENCE_USER, $confirmedUser);
        $manager->persist($confirmedUser);

        $confirmedUser = $this->getConfirmedUser()
            ->viaEmail(new Email('test-user-2@app.test'), $hash)
            ->withName(new Name('test-first-name-2', 'test-last-name-2'))
            ->build();

        $this->setReference(self::REFERENCE_USER_2, $confirmedUser);
        $manager->persist($confirmedUser);

        $invalidToken = $this->getConfirmedUser()
            ->viaEmail(new Email('invalid-new-email-token-user@app.test'), $hash)
            ->withNewEmail(new Email('test@test.ru'), '123')
            ->build();

        $manager->persist($invalidToken);

        $networkUser = $this->getConfirmedUser()
            ->viaEmail(new Email('user-with-network@app.test'), $hash)
            ->build();

        $networkUser->attachNetwork('facebook', '0001');

        $manager->persist($networkUser);

        $confirmed = $this->getConfirmedUser()
            ->viaEmail(null, $hash)
            ->build();

        $manager->persist($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-login@app.test'), $hash)
            ->build();

        $manager->persist($notConfirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-email@email.test'))
            ->build();

        $manager->persist($notConfirmed);

        $alreadyRequested = $this->getConfirmedUser()
            ->viaEmail(new Email('already-requested@email.test'))
            ->withResetToken($this->tokenizer->generate())
            ->build();

        $manager->persist($alreadyRequested);

        $resetToken   = new ResetToken('123', (new DateTimeImmutable())->add(new DateInterval('PT1H')));
        $expiredToken = new ResetToken('456', new DateTimeImmutable());

        $requested = $this->getConfirmedUser()
            ->viaEmail(new Email('request-reset-token@email.test'))
            ->withResetToken($resetToken)
            ->build();

        $manager->persist($requested);

        $expired = $this->getConfirmedUser()
            ->viaEmail(new Email('expired-token@email.test'))
            ->withResetToken($expiredToken)
            ->build();

        $manager->persist($expired);

        $existing = $this->getConfirmedUser()
            ->viaEmail(new Email('existing-user@app.test'))
            ->build();

        $manager->persist($existing);

        $confirmed = $this->getConfirmedUser()
            ->viaEmail(new Email('confirmed@app.test'), null, 'confirmed-token')
            ->build();

        $manager->persist($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-confirm@app.test'), null, 'not-confirmed-token')
            ->build();

        $manager->persist($notConfirmed);

        $confirmedUser = $this->getConfirmedUser()
            ->viaEmail(new Email('test-user-with-many-posts@app.test'), $hash)
            ->build();

        $this->setReference(self::REFERENCE_USER_3, $confirmedUser);
        $manager->persist($confirmedUser);

        $manager->flush();
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
     * @return array
     */
    public static function userWithManyPostsCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'test-user-with-many-posts@app.test',
            'PHP_AUTH_PW'   => '123qwe',
        ];
    }

    /**
     * @return array
     */
    public static function userWithManyCommentsCredentials(): array
    {
        return [
            'PHP_AUTH_USER' => 'test-user-2@app.test',
            'PHP_AUTH_PW'   => '123qwe',
        ];
    }

    /**
     * @return UserBuilder
     */
    private function getConfirmedUser(): UserBuilder
    {
        return (new UserBuilder())->confirmed();
    }
}
