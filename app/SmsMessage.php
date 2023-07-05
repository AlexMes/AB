<?php

namespace App;

use App\Events\Sms\SmsMessageCreated;
use App\Jobs\SMS\UpdateMessageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Throwable;

/**
 * App\SmsMessage
 *
 * @property int $id
 * @property int|null $campaign_id
 * @property int|null $lead_id
 * @property string|null $external_id
 * @property string|null $phone
 * @property string|null $message
 * @property string|null $status
 * @property array|null $raw_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $cost
 * @property string|null $service
 * @property-read \App\SmsCampaign|null $campaign
 * @property-read \App\Lead|null $lead
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage newQuery()
 * @method static \Illuminate\Database\Query\Builder|SmsMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereRawResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SmsMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SmsMessage withoutTrashed()
 * @mixin \Eloquent
 */
class SmsMessage extends Model
{
    use SoftDeletes;

    public const STATUS_SENT   = 'sent';
    public const STATUS_OK     = 'delivered';
    public const STATUS_FAILED = 'failed';
    public const STATUS_QUEUE  = 'queue';
    public const COST          = 0.13;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'sms_messages';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Cast model attributes to native type
     *
     * @var array
     */
    protected $casts = [
        'raw_response' => 'array'
    ];

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => SmsMessageCreated::class,
    ];

    /**
     * Related message campaign
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(SmsCampaign::class, 'campaign_id');
    }

    /**
     * Related message lead
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Determine is message have vendor id
     *
     * @return bool
     */
    public function hasVendorId(): bool
    {
        return $this->getVendorId() !== null;
    }

    /**
     * Get message ID from raw response
     *
     * @return mixed|null
     */
    public function getVendorId()
    {
        try {
            return array_keys($this->raw_response['success_request']['info'])[0];
        } catch (Throwable $exception) {
            return null;
        }
    }

    /**
     * Update message status
     *
     * @param int $delay
     *
     * @return void
     */
    public function updateStatus($delay = 0)
    {
        UpdateMessageStatus::dispatch($this)->onQueue(AdsBoard::QUEUE_SMS)->delay($delay);
    }
}
