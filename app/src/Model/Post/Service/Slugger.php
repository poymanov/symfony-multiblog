<?php

declare(strict_types=1);

namespace App\Model\Post\Service;

use Ausi\SlugGenerator\SlugGenerator;

class Slugger
{
    private SlugGenerator $generator;

    /**
     * @param SlugGenerator $generator
     */
    public function __construct(SlugGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function create(string $text): string
    {
        return $this->generator->generate($text);
    }
}
