<?php

declare(strict_types=1);

namespace App\Model\Post\Entity\Post;

use Webmozart\Assert\Assert;

class AuthorId
{
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
     * @param AuthorId $id
     * @return bool
     */
    public function isEqualTo(self $id): bool
    {
        return $this->getValue() === $id->getValue();
    }
}
