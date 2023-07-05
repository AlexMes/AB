<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CRM\Timezone
 *
 * @property int $id
 * @property string|null $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereName($value)
 * @mixin \Eloquent
 */
class Timezone extends Model
{
    use \Sushi\Sushi;

    protected $rows = [
        ['name' => 'мск-1'],
        ['name' => 'мск'],
        ['name' => 'мск+1'],
        ['name' => 'мск+2'],
        ['name' => 'мск+3'],
        ['name' => 'мск+4'],
        ['name' => 'мск+5'],
        ['name' => 'мск+6'],
        ['name' => 'мск+7'],
        ['name' => 'мск+8'],
        ['name' => 'мск+9'],
    ];
}
