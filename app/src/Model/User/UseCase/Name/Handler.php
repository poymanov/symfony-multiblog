<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Name;

use App\Model\Flusher;
use App\Model\Post\Service\Slugger;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\UserRepository;
use DomainException;

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
     * @var Slugger
     */
    private Slugger $slugger;

    /**
     * @param UserRepository $users
     * @param Flusher        $flusher
     * @param Slugger        $slugger
     */
    public function __construct(UserRepository $users, Flusher $flusher, Slugger $slugger)
    {
        $this->users   = $users;
        $this->flusher = $flusher;
        $this->slugger = $slugger;
    }

    /**
     * @param Command $command
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $name = new Name($command->first, $command->last);

        $alias = $this->slugger->create($name->getFull());

        if ($this->users->hasByAlias($alias)) {
            throw new DomainException('Пользователь с alias "' . $alias . '" уже существует.');
        }

        $user->changeName($name);
        $user->changeAlias($alias);

        $this->flusher->flush();
    }
}
