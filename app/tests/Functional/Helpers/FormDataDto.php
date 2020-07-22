<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers;

class FormDataDto
{
    private array $data;

    private string $submitButtonTitle;

    /**
     * @param array  $data
     * @param string $submitButtonTitle
     */
    public function __construct(array $data, string $submitButtonTitle = 'Отправить')
    {
        $this->data              = $data;
        $this->submitButtonTitle = $submitButtonTitle;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getSubmitButtonTitle(): string
    {
        return $this->submitButtonTitle;
    }
}
