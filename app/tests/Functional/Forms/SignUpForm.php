<?php

declare(strict_types=1);

namespace App\Tests\Functional\Forms;

class SignUpForm extends AbstractForm
{
    const FIELD_FIRST_NAME = 'firstName';

    const FIELD_LAST_NAME = 'lastName';

    const FIELD_EMAIL = 'email';

    const FIELD_PASSWORD = 'password';

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return [
            self::FIELD_FIRST_NAME,
            self::FIELD_LAST_NAME,
            self::FIELD_EMAIL,
            self::FIELD_PASSWORD,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCorrectData(): array
    {
        $data = [
            self::FIELD_FIRST_NAME => 'Tom',
            self::FIELD_LAST_NAME => 'Bent',
            self::FIELD_EMAIL => 'tom-bent@app.test',
            self::FIELD_PASSWORD   => '123qwe',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getNotValidData(): array
    {
        $data = [
            self::FIELD_FIRST_NAME => '',
            self::FIELD_LAST_NAME => '',
            self::FIELD_EMAIL => 'not-email',
            self::FIELD_PASSWORD   => '123',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getExistingData(): array
    {
        $data = [
            self::FIELD_FIRST_NAME => 'existing',
            self::FIELD_LAST_NAME => 'user',
            self::FIELD_EMAIL => 'existing-user@app.test',
            self::FIELD_PASSWORD   => '123qwe',
        ];

        return $this->buildFormData($data);
    }
}
