<?php

declare(strict_types=1);

namespace App\Model\Post\Entity\Post;

use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::inArray($value, self::STATUSES);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param Status $status
     *
     * @return bool
     */
    public function isEqual(self $status): bool
    {
        return $this->getValue() === $status->getValue();
    }

    /**
     * @return static
     */
    public static function draft(): self
    {
        return new self(self::STATUS_DRAFT);
    }

    /**
     * @return static
     */
    public static function published(): self
    {
        return new self(self::STATUS_PUBLISHED);
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->getValue() == self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isPublish(): bool
    {
        return $this->getValue() == self::STATUS_PUBLISHED;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }
}
