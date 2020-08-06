<?php

declare(strict_types=1);

namespace App\Model\Like\UseCase\Like\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $author;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $entityType;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $entityId;

    /**
     * @param string $author
     * @param string $entityType
     * @param string $entityId
     */
    public function __construct(string $author, string $entityType, string $entityId)
    {
        $this->author = $author;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
    }
}
