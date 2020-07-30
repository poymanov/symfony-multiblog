<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Edit;

use App\Model\Post\Entity\Post\Post;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id;

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
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param Post $post
     *
     * @return static
     */
    public static function fromPost(Post $post): self
    {
        $command = new self($post->getId()->getValue());
        $command->title = $post->getTitle();
        $command->previewText = $post->getPreviewText();
        $command->text = $post->getText();

        return $command;
    }

}
