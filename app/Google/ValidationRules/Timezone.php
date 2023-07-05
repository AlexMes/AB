<?php

namespace App\Google\ValidationRules;

class Timezone extends BaseValidationRule
{
    /**
     * @inheritDoc
     */
    public function getConditionValues(): array
    {
        return [
            'мск-1',
            'мск',
            'мск+1',
            'мск+2',
            'мск+3',
            'мск+4',
            'мск+5',
            'мск+6',
            'мск+7',
            'мск+8',
            'мск+9'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStartColumnIndex(): int
    {
        return 11;
    }

    /**
     * @inheritDoc
     */
    public function getEndColumnIndex(): int
    {
        return 12;
    }
}
