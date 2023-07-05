<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CRM\Profession
 *
 * @property int $id
 * @property string|null $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Profession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profession query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profession whereName($value)
 * @mixin \Eloquent
 */
class Profession extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        ['name' => 'безработный'],
        ['name' => 'в декрете'],
        ['name' => 'пенсионер'],
        ['name' => 'студент'],
        ['name' => 'работает'],
        ['name' => 'инвалид'],
    ];
}
