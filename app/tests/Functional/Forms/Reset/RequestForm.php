<?php

declare(strict_types=1);

namespace App\Tests\Functional\Forms\Reset;

use App\Tests\Functional\Forms\AbstractForm;

class RequestForm extends AbstractForm
{
    const FIELD_EMAIL = 'email';

    /**
     * @inheritDoc
     */
    function getFields(): array
    {
        return [self::FIELD_EMAIL];
    }

    /**
     * @inheritDoc
     */
    public function getCorrectData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'user@app.test',
        ];

        return $this->buildFormData($data);
    }

    /**
     * Несуществующие данные
     *
     * @return array
     */
    public function getNotExistingData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'not-a-existing@email.test',
        ];

        return $this->buildFormData($data);
    }

    /**
     * Неподтвержденный email пользователя
     *
     * @return array
     */
    public function getNotConfirmedData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'not-confirmed-email@email.test',
        ];

        return $this->buildFormData($data);
    }

    /**
     * Неподтвержденный email пользователя
     *
     * @return array
     */
    public function getAlreadyRequestData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'already-requested@email.test',
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getNotValidData(): array
    {
        $data = [
            self::FIELD_EMAIL => 'not-a-email',
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
