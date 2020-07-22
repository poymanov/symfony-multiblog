<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\NewEmailConfirmTokenizer;
use App\Model\User\Service\NewEmailConfirmTokenSender;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use DomainException;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var NewEmailConfirmTokenizer
     */
    private $tokenizer;

    /**
     * @var NewEmailConfirmTokenSender
     */
    private $sender;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * Handler constructor.
     * @param UserRepository $users
     * @param NewEmailConfirmTokenizer $tokenizer
     * @param NewEmailConfirmTokenSender $sender
     * @param Flusher $flusher
     */
    public function __construct(UserRepository $users, NewEmailConfirmTokenizer $tokenizer, NewEmailConfirmTokenSender $sender, Flusher $flusher)
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $email = new Email($command->email);

        if ($this->users->hasByMail($email)) {
            throw new DomainException('Email уже используется.');
        }

        $user->requestEmailChanging($email, $token = $this->tokenizer->generate());

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
