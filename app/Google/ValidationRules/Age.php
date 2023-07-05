<?php

namespace App\Google\ValidationRules;

class Age extends BaseValidationRule
{
    /**
     * @inheritDoc
     */
    public function getConditionValues(): array
    {
        return [
            '18-24','25-34','35-44','45-54','55-65','66+',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStartColumnIndex(): int
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getEndColumnIndex(): int
    {
        return 11;
    }
}
