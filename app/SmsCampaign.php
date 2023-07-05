<?php

namespace App;

use App\Jobs\RunSmsCampaign;
use App\Jobs\SMS\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\SmsCampaign
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property string|null $type
 * @property int|null $after_minutes
 * @property int|null $landing_id
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $branch_id
 * @property-read \App\Branch|null $branch
 * @property-read \App\Domain|null $landing
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SmsMessage[] $messages
 * @property-read int|null $messages_count
 *
 * @method static Builder|SmsCampaign active()
 * @method static Builder|SmsCampaign delayed()
 * @method static Builder|SmsCampaign instant()
 * @method static Builder|SmsCampaign newModelQuery()
 * @method static Builder|SmsCampaign newQuery()
 * @method static \Illuminate\Database\Query\Builder|SmsCampaign onlyTrashed()
 * @method static Builder|SmsCampaign query()
 * @method static Builder|SmsCampaign whereAfterMinutes($value)
 * @method static Builder|SmsCampaign whereBranchId($value)
 * @method static Builder|SmsCampaign whereCreatedAt($value)
 * @method static Builder|SmsCampaign whereDeletedAt($value)
 * @method static Builder|SmsCampaign whereId($value)
 * @method static Builder|SmsCampaign whereLandingId($value)
 * @method static Builder|SmsCampaign whereStatus($value)
 * @method static Builder|SmsCampaign whereText($value)
 * @method static Builder|SmsCampaign whereTitle($value)
 * @method static Builder|SmsCampaign whereType($value)
 * @method static Builder|SmsCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SmsCampaign withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SmsCampaign withoutTrashed()
 * @mixin \Eloquent
 */
class SmsCampaign extends Model
{
    use SoftDeletes;

    /** @var string  */
    public const INSTANT  = 'instant';

    /** @var string  */
    public const AFTER_TIME   = 'delayed';

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'sms_campaigns';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Load relationships count on every query
     *
     * @var array
     */
    protected $withCount = ['messages'];

    /**
     * Load relationships count on every query
     *
     * @var array
     */
    protected $with = ['landing'];

    /**
     * Assigned messages
     *
     * @return HasMany
     */
    public function messages()
    {
        return $this->hasMany(SmsMessage::class, 'campaign_id')
            ->orderByDesc('created_at');
    }

    /**
     * Landing page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function landing()
    {
        return  $this->belongsTo(Domain::class, 'landing_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * get only active campaigns
     *
     * @param $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', true);
    }

    /**
     * Get instant campaigns
     *
     * @param $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInstant(Builder $query)
    {
        return $query->where('type', self::INSTANT);
    }

    /**
     * Get delayed campaigns
     *
     * @param $query
     *
     * @return Builder|Builder
     */
    public function scopeDelayed(Builder $query)
    {
        return $query->where('type', self::AFTER_TIME);
    }

    /**
     * Send sms form campaign
     *
     * @param Lead $lead
     *
     * @return Model
     */
    public function sendSMS(Lead $lead)
    {
        return $lead->valid ? $this->messages()->create([
            'phone'     => substr_replace($lead->phone, 7, 0, 1),
            'message'   => str_replace('{name}', $lead->firstname, $this->text),
            'lead_id'   => $lead->id
        ]) : null;
    }

    /**
     * Run sms campaign
     *
     * @return void
     */
    public function run()
    {
        RunSmsCampaign::dispatch($this)->onQueue(AdsBoard::QUEUE_SMS);
    }

    /**
     * Schedule sms to the lead.
     * Probably must be named better
     *
     * @param \App\Lead $lead
     */
    public function dispatch(Lead $lead)
    {
        Message::dispatch($lead, $this)
            ->delay(now()->addMinutes($this->after_minutes))
            ->onQueue(AdsBoard::QUEUE_SMS);
    }
}
