<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CRM\Reason
 *
 * @property int $id
 * @property string|null $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Reason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reason query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reason whereName($value)
 * @mixin \Eloquent
 */
class Reason extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        ['name' => 'нет 18'],
        ['name' => 'неадекват'],
        ['name' => 'отзывы'],
        ['name' => 'сброс'],
        ['name' => 'нбт'],
        ['name' => 'нерезидент'],
        ['name' => 'в работе у другого менеджера'],
        ['name' => 'случайная регистрация'],
        ['name' => 'думал без денег можно'],
        ['name' => 'Не говорит по русски'],
    ];
}
