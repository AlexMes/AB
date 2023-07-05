<?php

namespace App\Google\ValidationRules;

class Profession extends BaseValidationRule
{
    /**
     * @inheritDoc
     */
    public function getConditionValues(): array
    {
        return [
            'безработный',
            'в декрете',
            'пенсионер',
            'студент',
            'работает',
            'инвалид',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStartColumnIndex(): int
    {
        return 9;
    }

    /**
     * @inheritDoc
     */
    public function getEndColumnIndex(): int
    {
        return 10;
    }
}
