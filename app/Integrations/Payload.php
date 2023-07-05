<?php

namespace App\Integrations;

use App\Domain;
use App\Lead;
use App\Offer;
use Illuminate\Database\Eloquent\Model;
use Zttp\ZttpResponse;

/**
 * App\Integrations\Payload
 *
 * @property int $id
 * @property int $form_id
 * @property int|null $offer_id
 * @property int $lead_id
 * @property string|null $external_lead_id
 * @property string|null $requestUrl
 * @property array|null $requestContents
 * @property int|null $responseStatus
 * @property array|null $responseHeaders
 * @property string|null $responseContents
 * @property string|null $status
 * @property array|null $data
 * @property string|null $runtime
 * @property string|null $sent_at
 * @property string|null $failed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $landing_id
 * @property-read \App\Integrations\Form $form
 * @property-read Domain|null $landing
 * @property-read Lead $lead
 * @property-read Offer|null $offer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Payload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payload query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereExternalLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereLandingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereRequestContents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereRequestUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereResponseContents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereResponseHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereResponseStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereRuntime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payload extends Model
{
    public const PREPARED = 'prepared';
    public const SENT     = 'sent';
    public const FAILED   = 'failed';
    public const FATAL    = 'fatal';

    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'integration_payloads';

    /**
     * Guard attributes from mass-assignment
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Cast attributes to types
     *
     * @var array
     */
    protected $casts = [
        'data'            => 'array',
        'requestContents' => 'array',
        'responseHeaders' => 'array',
    ];

    /**
     * Lead related to payloads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * Related to offer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    /**
     * Related form
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    /**
     * Related landing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function landing()
    {
        return $this->belongsTo(Domain::class, 'landing_id');
    }


    /**
     * @param ZttpResponse $response
     * @param boolean      $success
     *
     * @return Payload
     */
    protected function record(ZttpResponse $response, $success = true)
    {
        return tap($this)->update([
            'requestUrl'              => $response->effectiveUri(),
            'requestContents'         => $this->data,
            'responseStatus'          => $response->status(),
            'responseHeaders'         => $response->headers(),
            'responseContents'        => $response->body(),
            'status'                  => $success ? self::SENT : self::FAILED,
            'runtime'                 => null,
            'sent_at'                 => now(),
            'failed_at'               => $success ? null : now(),
            'external_lead_id'        => $this->form->getProvider()->getExternalId($response)
        ]);
    }

    /**
     * @return $this
     */
    protected function setLeadExternalsId()
    {
        if ($this->external_lead_id) {
            $this->lead->update(['external_id' => $this->external_lead_id]);
        }

        return $this;
    }

    /**
     * Record success response from vendor url
     *
     * @param ZttpResponse $response
     *
     * @return Payload
     */
    public function succeeded(ZttpResponse $response)
    {
        return $this->record($response)->setLeadExternalsId();
    }

    /**
     * @param ZttpResponse $response
     *
     * @return Payload
     */
    public function failed(ZttpResponse $response)
    {
        return $this->record($response, false);
    }

    /**
     * Catch connection failures.
     *
     * @param \Throwable $throwable
     *
     * @return Payload
     */
    public function fatal(\Throwable $throwable)
    {
        return tap($this)->update([
            'responseStatus'     => null,
            'status'             => static::FATAL,
            'responseContents'   => $throwable->getMessage(),
            'sent_at'            => now(),
            'failed_at'          => now()
        ]);
    }
}
