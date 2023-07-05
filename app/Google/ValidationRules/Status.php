<?php

namespace App\Google\ValidationRules;

class Status extends BaseValidationRule
{
    /**
     * @inheritDoc
     */
    public function getConditionValues(): array
    {
        return [
            'Новый',
            'Отказ',
            'В работе у клоузера',
            'Нет ответа',
            'Демонстрация',
            'Депозит',
            'Перезвон',
            'Дубль',
            'Неверный номер',
            'Резерв',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getStartColumnIndex(): int
    {
        return 6;
    }

    /**
     * @inheritDoc
     */
    public function getEndColumnIndex(): int
    {
        return 7;
    }
}
