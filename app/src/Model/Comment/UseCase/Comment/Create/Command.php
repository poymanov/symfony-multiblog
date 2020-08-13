<?php

declare(strict_types=1);

namespace App\Model\Comment\UseCase\Comment\Create;

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
     * @var string
     * @Assert\NotBlank()
     */
    public $text;

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
