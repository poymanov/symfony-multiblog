<?php

declare(strict_types=1);

namespace App\Tests\Functional\Forms\Profile;

use App\Tests\Functional\Forms\AbstractForm;

class ChangeEmailForm extends AbstractForm
{
    const FIELD_EMAIL = 'email';

    function getFields(): array
    {
        return [self::FIELD_EMAIL];
    }

    /**
     * Данные с пустыми значениями для формы
     *
     * @return array
     */
    public function getEmptyData(): array
    {
        $data = [
            self::FIELD_EMAIL => '',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getCorrectData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'user2@app.test',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getNotValidData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'not-email',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getExistingData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'user@app.test',
        ];

        return $this->buildFormData($data);
    }
}
