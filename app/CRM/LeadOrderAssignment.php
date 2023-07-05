<?php

namespace App\CRM;

/**
 * App\CRM\LeadOrderAssignment
 *
 * @property int $id
 * @property int $route_id
 * @property int $lead_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $confirmed_at
 * @property int|null $destination_id
 * @property string|null $delivery_failed
 * @property string|null $redirect_url
 * @property int|null $gender_id
 * @property string|null $external_id
 * @property \Illuminate\Support\Carbon|null $called_at
 * @property string|null $timezone
 * @property string|null $age
 * @property string|null $profession
 * @property string|null $reject_reason
 * @property string|null $status
 * @property string|null $comment
 * @property string|null $deposit_sum
 * @property string|null $alt_phone
 * @property string|null $benefit
 * @property string|null $frx_call_id
 * @property string|null $frx_lead_id
 * @property \Illuminate\Support\Carbon|null $registered_at
 * @property bool|null $is_live
 * @property bool $is_payable
 * @property \Illuminate\Support\Carbon|null $deliver_at
 * @property bool $simulate_autologin
 * @property int|null $replace_auth_id
 * @property string|null $smooth_sort
 * @property-read \App\ResellBatch|null $batch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CRM\Callback[] $callbacks
 * @property-read int|null $callbacks_count
 * @property-read \App\LeadDestination|null $destination
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read string|null $actual_benefit
 * @property-read \Illuminate\Support\Carbon|null $actual_time
 * @property-read bool $autologin
 * @property-read string $formatted_alt_phone
 * @property-read mixed $gender
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CRM\Label[] $labels
 * @property-read int|null $labels_count
 * @property-read \App\Lead $lead
 * @property-read \App\LeadOrderRoute $route
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CRM\Callback[] $scheduledCallbacks
 * @property-read int|null $scheduled_callbacks_count
 *
 * @method static Builder|LeadOrderAssignment allowedOffers()
 * @method static Builder|LeadOrderAssignment confirmed()
 * @method static Builder|LeadOrderAssignment forOffer($offer = null)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment newQuery()
 * @method static Builder|LeadOrderAssignment payable()
 * @method static Builder|LeadOrderAssignment pendingDelayed()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment query()
 * @method static Builder|LeadOrderAssignment undelivered()
 * @method static Builder|LeadOrderAssignment visible()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereAltPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereBenefit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereCalledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereDeliverAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereDeliveryFailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereDepositSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereDestinationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereFrxCallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereFrxLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereGenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereIsLive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereIsPayable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereProfession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereRedirectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereRegisteredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereReplaceAuthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereSimulateAutologin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereSmoothSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadOrderAssignment whereUpdatedAt($value)
 * @method static Builder|LeadOrderAssignment withDeliveryTry()
 * @method static Builder|LeadOrderAssignment withoutDeliveryFailed(?string $message = null)
 * @method static Builder|LeadOrderAssignment withoutDeliveryTry()
 * @mixin \Eloquent
 */
class LeadOrderAssignment extends \App\LeadOrderAssignment
{
    //
}
