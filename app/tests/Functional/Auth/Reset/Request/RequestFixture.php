<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Reset\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Service\ResetTokenizer;
use App\Tests\Builder\User\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class RequestFixture extends Fixture
{
    /**
     * @var ResetTokenizer
     */
    private $tokenizer;

    /**
     * @param ResetTokenizer $tokenizer
     */
    public function __construct(ResetTokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $notConfirmed = (new UserBuilder())
            ->viaEmail(new Email('not-confirmed-email@email.test'))
            ->build();

        $manager->persist($notConfirmed);

        $alreadyRequested = (new UserBuilder())
            ->viaEmail(new Email('already-requested@email.test'))
            ->confirmed()
            ->withResetToken($this->tokenizer->generate())
            ->build();

        $manager->persist($alreadyRequested);

        $manager->flush();
    }
}
