<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CRM\TenantLeadStats
 *
 * @property int $id
 * @property int $assignment_id
 * @property bool $exists
 * @property string|null $status
 * @property string|null $result
 * @property string|null $closer
 * @property bool|null $manager_can_view
 * @property string|null $last_called_at
 * @property string|null $last_viewed_at
 * @property string|null $last_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereCloser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereExists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereLastCalledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereLastUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereLastViewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereManagerCanView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantLeadStats whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TenantLeadStats extends Model
{
    /**
     * Model table name in database
     *
     * @var string
     */
    protected $table = 'tcrm_frx_tenant_lead_stats';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
