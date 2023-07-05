<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\CRM\Status
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $color
 * @property string|null $as_column
 *
 * @method static Builder|Status newModelQuery()
 * @method static Builder|Status newQuery()
 * @method static Builder|Status query()
 * @method static Builder|Status selectable()
 * @method static Builder|Status whereAsColumn($value)
 * @method static Builder|Status whereColor($value)
 * @method static Builder|Status whereId($value)
 * @method static Builder|Status whereName($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    use \Sushi\Sushi;

    public const COLOR_GRAY              = 'gray';
    public const COLOR_RED               = 'red';
    public const COLOR_ORANGE            = 'orange';
    public const COLOR_YELLOW            = 'yellow';
    public const COLOR_GREEN             = 'green';
    public const COLOR_TEAL              = 'teal';
    public const COLOR_BLUE              = 'blue';
    public const COLOR_INDIGO            = 'indigo';
    public const COLOR_PURPLE            = 'purple';
    public const COLOR_PINK              = 'pink';
    public const COLOR_BLACK             = 'black';
    public const PASSTHROUGHS_VALIDATION = ['Нет ответа', 'Неверный номер', 'Дубль'];

    protected $rows = [
        ['name' => 'Новый', 'color' => self::COLOR_GRAY, 'as_column' => 'new'],
        ['name' => 'Отказ', 'color' => self::COLOR_RED, 'as_column' => 'reject'],
        ['name' => 'В работе у клоузера', 'color' => self::COLOR_TEAL, 'as_column' => 'on_closer'],
        ['name' => 'Нет ответа', 'color' => self::COLOR_YELLOW, 'as_column' => 'no_answer'],
        ['name' => 'Дозвонится', 'color' => self::COLOR_RED, 'as_column' => 'force_call'],
        ['name' => 'Демонстрация', 'color' => self::COLOR_PURPLE, 'as_column' => 'demo'],
        ['name' => 'Депозит', 'color' => self::COLOR_PINK, 'as_column' => 'deposits'],
        ['name' => 'Перезвон', 'color' => self::COLOR_GREEN, 'as_column' => 'callback'],
        ['name' => 'Дубль', 'color' => self::COLOR_BLACK, 'as_column' => 'double'],
        ['name' => 'Неверный номер', 'color' => self::COLOR_BLACK, 'as_column' => 'wrong_nb'],
        ['name' => 'Резерв','color' => self::COLOR_BLUE, 'as_column' => 'reserve'],
        ['name' => 'Меньше 18','color' => self::COLOR_BLACK, 'as_column' => 'under_18'],
        ['name' => 'Не говорит по-русски','color' => self::COLOR_BLACK, 'as_column' => 'wrong_lang'],
        ['name' => 'Не резидент','color' => self::COLOR_BLACK, 'as_column' => 'not_resident'],
        ['name' => 'На замену','color' => self::COLOR_BLACK, 'as_column' => 'for_replacement'],
    ];

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeSelectable(Builder $builder)
    {
        if (auth('crm')->check() && auth('crm')->user()->office_id) {
            $builder->whereIn('name', auth('crm')->user()->office->statuses()->whereSelectable(true)->pluck('status'));
        }

        return $builder;
    }
}
