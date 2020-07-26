<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use BadMethodCallException;
use DateTimeImmutable;
use Exception;

class UserBuilder
{
    /**
     * @var Id
     */
    private $id;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var Name
     */
    private $name;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $confirmed;

    /**
     * @var string
     */
    private $network;

    /**
     * @var string
     */
    private $identity;

    /**
     * @var Role
     */
    private $role;

    /**
     * @var ResetToken
     */
    private $resetToken;

    /**
     * @var Email
     */
    private $newEmail;

    /**
     * @var string
     */
    private $newEmailToken;


    /**
     * UserBuilder constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->id   = Id::next();
        $this->date = new DateTimeImmutable();
        $this->name = new Name('First', 'Last');
    }

    /**
     * @param Email|null  $email
     * @param string|null $hash
     * @param string|null $token
     *
     * @return $this
     */
    public function viaEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $clone        = clone $this;
        $clone->email = $email ?? new Email('mail@app.test');
        $clone->hash  = $hash ?? 'hash';
        $clone->token = $token ?? 'token';

        return $clone;
    }

    /**
     * @param string|null $network
     * @param string|null $identity
     *
     * @return $this
     */
    public function viaNetwork(string $network = null, string $identity = null): self
    {
        $clone           = clone $this;
        $clone->network  = $network ?? 'facebook';
        $clone->identity = $identity ?? '0001';

        return $clone;
    }

    /**
     * @return $this
     */
    public function confirmed(): self
    {
        $clone            = clone $this;
        $clone->confirmed = true;

        return $clone;
    }

    /**
     * @param Id $id
     *
     * @return $this
     */
    public function withId(Id $id): self
    {
        $clone     = clone $this;
        $clone->id = $id;

        return $clone;
    }

    /**
     * @param Name $name
     *
     * @return $this
     */
    public function withName(Name $name): self
    {
        $clone       = clone $this;
        $clone->name = $name;

        return $clone;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function withRole(Role $role): self
    {
        $clone       = clone $this;
        $clone->role = $role;

        return $clone;
    }

    /**
     * @param ResetToken $resetToken
     *
     * @return $this
     */
    public function withResetToken(ResetToken $resetToken): self
    {
        $clone             = clone $this;
        $clone->resetToken = $resetToken;

        return $clone;
    }

    /**
     * @param string $newEmail
     * @param string $newEmailToken
     *
     * @return $this
     */
    public function withNewEmail(Email $newEmail, string $newEmailToken): self
    {
        $clone       = clone $this;
        $clone->newEmail = $newEmail;
        $clone->newEmailToken = $newEmailToken;

        return $clone;
    }

    /**
     * @return User
     * @throws Exception
     */
    public function build(): User
    {
        if ($this->email) {
            $user = User::signUpByEmail(
                $this->id,
                $this->date,
                $this->name,
                $this->email,
                $this->hash,
                $this->token
            );

            if ($this->confirmed) {
                $user->confirmSignUp();
            }
        }

        if ($this->network) {
            $user = User::signUpByNetwork(
                $this->id,
                $this->date,
                $this->name,
                $this->network,
                $this->identity
            );
        }

        if (!$user) {
            throw new BadMethodCallException('Specify via method.');
        }

        if ($this->role) {
            $user->changeRole($this->role);
        }

        if ($this->resetToken) {
            $user->requestPasswordReset($this->resetToken, new DateTimeImmutable());
        }

        if ($this->newEmail && $this->newEmailToken) {
            $user->requestEmailChanging($this->newEmail, $this->newEmailToken);
        }

        return $user;
    }
}
