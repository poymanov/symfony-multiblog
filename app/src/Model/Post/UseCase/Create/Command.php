<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Create;

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
    public $title;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $previewText;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $text;

    /**
     * @param string $author
     */
    public function __construct(string $author)
    {
        $this->author = $author;
    }
}
