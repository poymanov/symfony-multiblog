<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

class Command
{
    /**
     * @var string
     */
    public $network;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $identity;

    /**
     * Command constructor.
     * @param string $network
     * @param string $identity
     */
    public function __construct(string $network, string $identity)
    {
        $this->network = $network;
        $this->identity = $identity;
    }
}
