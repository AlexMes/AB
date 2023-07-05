<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LeadMarker
 *
 * @property int $id
 * @property string $name
 * @property int $lead_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Lead $lead
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadMarker whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadMarker extends Model
{
    /**
     * Protect model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Related lead for marker
     *
     * @return void
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
