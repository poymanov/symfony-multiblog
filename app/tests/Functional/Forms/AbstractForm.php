<?php

declare(strict_types=1);

namespace App\Tests\Functional\Forms;

abstract class AbstractForm
{
    public string $submitButtonTitle = 'Отправить';

    public string $formName = 'form';

    /**
     * Список полей формы
     *
     * @return array
     */
    abstract function getFields(): array;

    /**
     * Правильные значения формы
     *
     * @return array
     */
    abstract public function getCorrectData(): array;

    /**
     * Неправильные значения формы
     *
     * @return array
     */
    abstract public function getNotValidData(): array;

    /**
     * Значение формы для уже существующего пользователя
     *
     * @return array
     */
    abstract public function getExistingData(): array;

    /**
     * Формирование данных для отправки формы
     *
     * @param array $data
     *
     * @return array
     */
    protected function buildFormData(array $data): array
    {
        $formData = [];

        foreach ($data as $key => $value) {
            $formData[$this->formName . '[' . $key . ']'] = $value;
        }

        return $formData;
    }
}
