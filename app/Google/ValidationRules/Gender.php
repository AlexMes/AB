<?php

namespace App\Google\ValidationRules;

class Gender extends BaseValidationRule
{
    /**
     * @inheritDoc
     */
    public function getConditionValues(): array
    {
        return [
            'М',
            'Ж',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStartColumnIndex(): int
    {
        return 8;
    }

    /**
     * @inheritDoc
     */
    public function getEndColumnIndex(): int
    {
        return 9;
    }
}
