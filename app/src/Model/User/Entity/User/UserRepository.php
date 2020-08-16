<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repo;

    /**
     * UserRepository constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
        $this->repo = $em->getRepository(User::class);
    }

    /**
     * @param string $token
     *
     * @return User|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    public function findByResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    /**
     * @param Id $id
     *
     * @return User
     */
    public function get(Id $id): User
    {
        if (!$user = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Пользователь не найден.');
        }

        return $user;
    }

    /**
     * @param Email $email
     *
     * @return User
     */
    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repo->findOneBy(['email' => $email->getValue()])) {
            throw new EntityNotFoundException('Пользователь не найден.');
        }

        return $user;
    }

    /**
     * @param Email $email
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByMail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param string $alias
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByAlias(string $alias): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.alias = :alias')
                ->setParameter(':alias', $alias)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param string $network
     * @param string $identity
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.networks', 'n')
                ->andWhere('n.network = :network and n.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param User $user
     */
    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}
