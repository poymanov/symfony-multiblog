<?php

declare(strict_types=1);

namespace App\Model\Like\Entity\Like;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Entity
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @param string $type
     * @param string $id
     */
    public function __construct(string $type, string $id)
    {
        Assert::notEmpty($type);
        Assert::notEmpty($id);

        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
