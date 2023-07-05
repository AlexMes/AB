<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\InitialPlan
 *
 * @property int $id
 * @property string $date
 * @property string $leads_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan whereLeadsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InitialPlan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InitialPlan extends Model
{
    protected $guarded = [];
}
