<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CRM\Age
 *
 * @property int $id
 * @property string|null $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Age newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Age newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Age query()
 * @method static \Illuminate\Database\Eloquent\Builder|Age whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Age whereName($value)
 * @mixin \Eloquent
 */
class Age extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        ['name' => '18-24'],
        ['name' => '25-34'],
        ['name' => '35-44'],
        ['name' => '45-54'],
        ['name' => '55-65'],
        ['name' => '66+'],
    ];
}
