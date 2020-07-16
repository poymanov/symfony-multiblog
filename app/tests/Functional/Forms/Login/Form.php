<?php

declare(strict_types=1);

namespace App\Tests\Functional\Forms\Login;

use App\Tests\Functional\Forms\AbstractForm;

class Form extends AbstractForm
{
    const FIELD_EMAIL = 'email';

    const FIELD_PASSWORD = 'password';

    const REMEMBER_ME = '_remember_me';

    public ?string $formName = null;

    /**
     * @inheritDoc
     */
    function getFields(): array
    {
        return [self::FIELD_EMAIL, self::FIELD_PASSWORD, self::REMEMBER_ME];
    }

    /**
     * @inheritDoc
     */
    public function getCorrectData(): array
    {
        $data = [
            self::FIELD_EMAIL    => 'mail@app.test',
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
            self::FIELD_EMAIL    => 'mail@app.test',
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

    /**
     * Получение данных ещё не подтвержденного пользователя
     */
    public function getNotConfirmedData(): array
    {
        $data = [
            self::FIELD_EMAIL    => 'not-confirmed@app.test',
            self::FIELD_PASSWORD => '123qwe',
        ];

        return $this->buildFormData($data);
    }

    /**
     * Получение данных несуществующего пользователя
     *
     * @return array
     */
    public function getNotExistedData(): array
    {
        $data = [
            self::FIELD_EMAIL    => 'not-email',
            self::FIELD_PASSWORD => '123',
        ];

        return $this->buildFormData($data);
    }
}
