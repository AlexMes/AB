<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AssignmentDayToDaySnapshot
 *
 * @property int $id
 * @property string $date
 * @property int $manager_id
 * @property int $offer_id
 * @property string $manager
 * @property string $offer
 * @property int $total
 * @property int $deposits
 * @property int $no_answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereDeposits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereNoAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentDayToDaySnapshot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AssignmentDayToDaySnapshot extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'assignment_day_to_day_snapshots';
}
