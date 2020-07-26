<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        if ($this->users->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new DomainException('Социальная сеть уже подключена.');
        }

        $user = $this->users->get(new Id($command->user));

        $user->attachNetwork($command->network, $command->identity);

        $this->flusher->flush();
    }

}
