<?php

declare(strict_types=1);

namespace App\Tests\Functional\Forms\Profile;

use App\Tests\Functional\Forms\AbstractForm;
use Faker;

class ChangeNameForm extends AbstractForm
{
    const FIELD_FIRST = 'first';

    const FIELD_LAST = 'last';

    function getFields(): array
    {
        return [self::FIELD_FIRST, self::FIELD_LAST];
    }

    /**
     * Данные с пустыми значениями для формы
     *
     * @return array
     */
    public function getEmptyData(): array
    {
        $data = [
            self::FIELD_FIRST => '',
            self::FIELD_LAST  => '',
        ];

        return $this->buildFormData($data);
    }

    /**
     * Данные с длинными значениями для формы
     *
     * @return array
     */
    public function getLongData(): array
    {
        $faker = Faker\Factory::create();

        $longData = $faker->paragraph(10);

        $data = [
            self::FIELD_FIRST => $longData,
            self::FIELD_LAST  => $longData,
        ];

        return $this->buildFormData($data);
    }

    /**
     * @inheritDoc
     */
    public function getCorrectData(): array
    {
        $faker = Faker\Factory::create();

        $data = [
            self::FIELD_FIRST => $faker->firstName,
            self::FIELD_LAST  => $faker->lastName,
        ];

        return $this->buildFormData($data);
    }

    public function getNotValidData(): array
    {
        // TODO: Implement getNotValidData() method.
    }

    public function getExistingData(): array
    {
        // TODO: Implement getExistingData() method.
    }
}
