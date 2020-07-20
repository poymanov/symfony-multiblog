<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Name;

use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public $first;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public $last;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param User $user
     * @return static
     */
    public static function fromUser(User $user): self
    {
        $command = new self($user->getId()->getValue());
        $command->first = $user->getName()->getFirst();
        $command->last = $user->getName()->getLast();

        return $command;
    }
}
