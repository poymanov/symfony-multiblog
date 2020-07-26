<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use DateTimeImmutable;
use DomainException;
use Exception;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        if ($this->users->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new DomainException('User already exists.');
        }

        $user = User::signUpByNetwork(
            Id::next(),
            new DateTimeImmutable(),
            new Name($command->firstName, $command->lastName),
            $command->network,
            $command->identity
        );

        $this->users->add($user);
        $this->flusher->flush();
    }
}
