<?php

namespace App\Google\ValidationRules;

class Note extends BaseValidationRule
{
    /**
     * @inheritDoc
     */
    public function getConditionValues(): array
    {
        return [
            'нет 18',
            'неадекват',
            'отзывы',
            'сброс',
            'нбт',
            'нерезидент',
            'в работе у другого менеджера',
            'случайная регистрация',
            'думал без денег можно',
            'Не говорит по русски',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStartColumnIndex(): int
    {
        return 7;
    }

    /**
     * @inheritDoc
     */
    public function getEndColumnIndex(): int
    {
        return 8;
    }
}
