<?php

declare(strict_types=1);

namespace App\Tests\Functional\Forms\Reset;

use App\Tests\Functional\Forms\AbstractForm;

class ResetForm extends AbstractForm
{
    const FIELD_PASSWORD = 'password';

    /**
     * @inheritDoc
     */
    function getFields(): array
    {
        return [self::FIELD_PASSWORD];
    }

    /**
     * @inheritDoc
     */
    public function getCorrectData(): array
    {
        $data = [
            self::FIELD_PASSWORD => '123qwe',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getNotValidData(): array
    {
        $data = [
            self::FIELD_PASSWORD => '123',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getExistingData(): array
    {
        return [];
    }
}
