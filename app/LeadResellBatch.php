<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\LeadResellBatch
 *
 * @property int $id
 * @property int $lead_id
 * @property int $batch_id
 * @property string|null $assigned_at
 * @property int|null $assignment_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadResellBatch whereLeadId($value)
 * @mixin \Eloquent
 */
class LeadResellBatch extends Pivot
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'lead_resell_batch';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];
}
