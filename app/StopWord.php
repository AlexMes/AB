<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StopWord
 *
 * @property int $id
 * @property string $keyword
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StopWord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StopWord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StopWord query()
 * @method static \Illuminate\Database\Eloquent\Builder|StopWord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StopWord whereKeyword($value)
 * @mixin \Eloquent
 */
class StopWord extends Model
{
    protected $table   = 'stop_words';
    protected $guarded = ['id'];
    public $timestamps = false;
}
