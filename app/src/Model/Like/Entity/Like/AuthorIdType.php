<?php

declare(strict_types=1);

namespace App\Model\Like\Entity\Like;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class AuthorIdType extends GuidType
{
    public const NAME = 'like_like_author_id';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof AuthorId ? $value->getValue(): $value;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return AuthorId|mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new AuthorId($value): null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}
