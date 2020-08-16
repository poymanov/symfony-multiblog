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
            ->withName(new Name('test-first-name-3', 'test-last-name-3'))
            ->build();

        $manager->persist($invalidToken);

        $networkUser = $this->getConfirmedUser()
            ->viaEmail(new Email('user-with-network@app.test'), $hash)
            ->withName(new Name('test-first-name-4', 'test-last-name-4'))
            ->build();

        $networkUser->attachNetwork('facebook', '0001');

        $manager->persist($networkUser);

        $confirmed = $this->getConfirmedUser()
            ->withName(new Name('test-first-name-5', 'test-last-name-5'))
            ->viaEmail(null, $hash)
            ->build();

        $manager->persist($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-login@app.test'), $hash)
            ->withName(new Name('test-first-name-6', 'test-last-name-6'))
            ->build();

        $manager->persist($notConfirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-email@email.test'))
            ->withName(new Name('test-first-name-7', 'test-last-name-7'))
            ->build();

        $manager->persist($notConfirmed);

        $alreadyRequested = $this->getConfirmedUser()
            ->viaEmail(new Email('already-requested@email.test'))
            ->withName(new Name('test-first-name-8', 'test-last-name-8'))
            ->withResetToken($this->tokenizer->generate())
            ->build();

        $manager->persist($alreadyRequested);

        $resetToken   = new ResetToken('123', (new DateTimeImmutable())->add(new DateInterval('PT1H')));
        $expiredToken = new ResetToken('456', new DateTimeImmutable());

        $requested = $this->getConfirmedUser()
            ->viaEmail(new Email('request-reset-token@email.test'))
            ->withName(new Name('test-first-name-9', 'test-last-name-9'))
            ->withResetToken($resetToken)
            ->build();

        $manager->persist($requested);

        $expired = $this->getConfirmedUser()
            ->viaEmail(new Email('expired-token@email.test'))
            ->withName(new Name('test-first-name-10', 'test-last-name-10'))
            ->withResetToken($expiredToken)
            ->build();

        $manager->persist($expired);

        $existing = $this->getConfirmedUser()
            ->viaEmail(new Email('existing-user@app.test'))
            ->withName(new Name('test-first-name-11', 'test-last-name-11'))
            ->build();

        $manager->persist($existing);

        $confirmed = $this->getConfirmedUser()
            ->viaEmail(new Email('confirmed@app.test'), null, 'confirmed-token')
            ->withName(new Name('test-first-name-12', 'test-last-name-12'))
            ->build();

        $manager->persist($confirmed);

        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-confirm@app.test'), null, 'not-confirmed-token')
            ->withName(new Name('test-first-name-13', 'test-last-name-13'))
            ->build();

        $manager->persist($notConfirmed);

        $confirmedUser = $this->getConfirmedUser()
            ->viaEmail(new Email('test-user-with-many-posts@app.test'), $hash)
            ->withName(new Name('test-first-name-14', 'test-last-name-14'))
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
