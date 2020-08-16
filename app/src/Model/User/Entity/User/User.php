<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Exception;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_users", uniqueConstraints={
 *  @ORM\UniqueConstraint(columns={"email"}),
 *  @ORM\UniqueConstraint(columns={"reset_token_token"})
 * })
 */
class User
{
    public const STATUS_WAIT = 'wait';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_BLOCKED = 'blocked';

    /**
     * @var Id
     * @ORM\Column(type="user_user_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @var Email|null
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="password_hash", nullable=true)
     */
    private $passwordHash;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="confirm_token", nullable=true)
     */
    private $confirmToken;

    /**
     * @var Name
     * @ORM\Embedded(class="Name")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $alias;

    /**
     * @var Email|null
     * @ORM\Column(type="user_user_email", name="new_email", nullable=true)
     */
    private $newEmail;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="new_email_token", nullable=true)
     */
    private $newEmailToken;

    /**
     * @var ResetToken|null
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;

    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    private $status;

    /**
     * @var Role
     * @ORM\Column(type="user_user_role", length=16)
     */
    private $role;

    /**
     * @var Network[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Network", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $networks;

    /**
     * @var int
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param Id                $id
     * @param DateTimeImmutable $date
     * @param Name              $name
     */
    private function __construct(Id $id, DateTimeImmutable $date, Name $name)
    {
        $this->id       = $id;
        $this->date     = $date;
        $this->name     = $name;
        $this->role     = Role::user();
        $this->networks = new ArrayCollection();
    }

    /**
     * @param Id                $id
     * @param DateTimeImmutable $date
     * @param Name              $name
     * @param string            $alias
     * @param Email             $email
     * @param string            $hash
     *
     * @return $this
     */
    public static function create(Id $id, DateTimeImmutable $date, Name $name, string $alias, Email $email, string $hash): self
    {
        $user               = new self($id, $date, $name);
        $user->email        = $email;
        $user->passwordHash = $hash;
        $user->status       = self::STATUS_ACTIVE;
        $user->alias        = $alias;

        return $user;
    }

    /**
     * @param Id                $id
     * @param DateTimeImmutable $date
     * @param Name              $name
     * @param string            $alias
     * @param Email             $email
     * @param string            $hash
     * @param string            $token
     *
     * @return $this
     */
    public static function signUpByEmail(Id $id, DateTimeImmutable $date, Name $name, string $alias, Email $email, string $hash, string $token): self
    {
        $user               = new self($id, $date, $name);
        $user->alias        = $alias;
        $user->email        = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status       = self::STATUS_WAIT;

        return $user;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Пользователь уже активирован.');
        }

        $this->status = self::STATUS_ACTIVE;
    }

    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new DomainException('Пользователь уже заблокирован.');
        }

        $this->status = self::STATUS_BLOCKED;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('Пользователь уже подтвержден.');
        }

        $this->status       = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    /**
     * @param Id                $id
     * @param DateTimeImmutable $date
     * @param Name              $name
     * @param string            $network
     * @param string            $identity
     *
     * @return $this
     * @throws Exception
     */
    public static function signUpByNetwork(Id $id, DateTimeImmutable $date, Name $name, string $network, string $identity): self
    {
        $user        = new self($id, $date, $name);
        $user->alias = 'user-' . $identity;
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;

        return $user;
    }

    /**
     * @param string $network
     * @param string $identity
     *
     * @throws Exception
     */
    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new DomainException('Сеть уже добавлена.');
            }
        }

        $this->networks->add(new Network($this, $network, $identity));
    }

    /**
     * @param string $network
     * @param string $identity
     */
    public function detachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isFor($network, $identity)) {
                if (!$this->email && $this->networks->count() === 1) {
                    throw new DomainException('Невозможно отсоединить основную сеть.');
                }
                $this->networks->removeElement($existing);

                return;
            }
        }
        throw new DomainException('Сеть не присоединена.');
    }

    /**
     * @param ResetToken        $token
     * @param DateTimeImmutable $date
     */
    public function requestPasswordReset(ResetToken $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('Пользователь ещё не активен.');
        }

        if (!$this->email) {
            throw new DomainException('Email не определен.');
        }

        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new DomainException('Сброс пароля уже запрошен.');
        }

        $this->resetToken = $token;
    }

    /**
     * @param DateTimeImmutable $date
     * @param string            $hash
     *
     * @return string
     */
    public function passwordReset(DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new DomainException('Сброс пароля не был запрошен.');
        }

        if ($this->resetToken->isExpiredTo($date)) {
            throw new DomainException('Токен сброса пароля уже истек.');
        }

        $this->passwordHash = $hash;
        $this->resetToken   = null;
    }

    /**
     * @param Email  $email
     * @param string $token
     */
    public function requestEmailChanging(Email $email, string $token): void
    {
        if (!$this->isActive()) {
            throw new DomainException('Пользователь ещё не активен.');
        }

        if ($this->email && $this->email->isEqual($email)) {
            throw new DomainException('Новый email совпадает с текущим.');
        }

        $this->newEmail      = $email;
        $this->newEmailToken = $token;
    }

    /**
     * @param string $token
     */
    public function confirmEmailChanging(string $token): void
    {
        if (!$this->newEmailToken) {
            throw new DomainException('Изменение email не было запрошено.');
        }

        if ($this->newEmailToken !== $token) {
            throw new DomainException('Неверный токен изменения пароля.');
        }

        $this->email         = $this->newEmail;
        $this->newEmail      = null;
        $this->newEmailToken = null;
    }

    /**
     * @param Name $name
     */
    public function changeName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $alias
     */
    public function changeAlias(string $alias): void
    {
        if ($this->getAlias() == $alias) {
            throw new DomainException('Alias пользователя остался без изменений.');
        }

        $this->alias = $alias;
    }

    /**
     * @param Email $email
     * @param Name  $name
     */
    public function edit(Email $email, Name $name): void
    {
        $this->name  = $name;
        $this->email = $email;
    }

    /**
     * @param Role $role
     */
    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new DomainException('Новая роль совпадает с текущей.');
        }

        $this->role = $role;
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return string|null
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return Email|null
     */
    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    /**
     * @return string|null
     */
    public function getNewEmailToken(): ?string
    {
        return $this->newEmailToken;
    }

    /**
     * @return ResetToken|null
     */
    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }
}
