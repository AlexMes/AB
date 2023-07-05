<?php

namespace App;

use App\CRM\Callback;
use App\CRM\Events\NewLeadAssigned;
use App\CRM\Label;
use App\CRM\Timezone;
use App\DestinationDrivers\CallResult;
use App\Events\AssignmentDeleted;
use App\Events\AssignmentSaved;
use App\Events\AssignmentTransferred;
use App\Events\AssignmentUpdating;
use App\Events\LeadAssigned;
use App\Events\LeadAssigning;
use App\Jobs\DeliverAssignment;
use App\Traits\RecordEvents;
use App\Traits\SaveQuietly;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\LeadOrderAssignment
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
 * @property-read \Illuminate\Database\Eloquent\Collection|Callback[] $callbacks
 * @property-read int|null $callbacks_count
 * @property-read \App\LeadDestination|null $destination
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read string|null $actual_benefit
 * @property-read \Illuminate\Support\Carbon|null $actual_time
 * @property-read bool $autologin
 * @property-read string $formatted_alt_phone
 * @property-read mixed $gender
 * @property-read \Illuminate\Database\Eloquent\Collection|Label[] $labels
 * @property-read int|null $labels_count
 * @property-read \App\Lead $lead
 * @property-read \App\LeadOrderRoute $route
 * @property-read \Illuminate\Database\Eloquent\Collection|Callback[] $scheduledCallbacks
 * @property-read int|null $scheduled_callbacks_count
 *
 * @method static Builder|LeadOrderAssignment allowedOffers()
 * @method static Builder|LeadOrderAssignment confirmed()
 * @method static Builder|LeadOrderAssignment forOffer($offer = null)
 * @method static Builder|LeadOrderAssignment newModelQuery()
 * @method static Builder|LeadOrderAssignment newQuery()
 * @method static Builder|LeadOrderAssignment payable()
 * @method static Builder|LeadOrderAssignment pendingDelayed()
 * @method static Builder|LeadOrderAssignment query()
 * @method static Builder|LeadOrderAssignment undelivered()
 * @method static Builder|LeadOrderAssignment visible()
 * @method static Builder|LeadOrderAssignment whereAge($value)
 * @method static Builder|LeadOrderAssignment whereAltPhone($value)
 * @method static Builder|LeadOrderAssignment whereBenefit($value)
 * @method static Builder|LeadOrderAssignment whereCalledAt($value)
 * @method static Builder|LeadOrderAssignment whereComment($value)
 * @method static Builder|LeadOrderAssignment whereConfirmedAt($value)
 * @method static Builder|LeadOrderAssignment whereCreatedAt($value)
 * @method static Builder|LeadOrderAssignment whereDeliverAt($value)
 * @method static Builder|LeadOrderAssignment whereDeliveryFailed($value)
 * @method static Builder|LeadOrderAssignment whereDepositSum($value)
 * @method static Builder|LeadOrderAssignment whereDestinationId($value)
 * @method static Builder|LeadOrderAssignment whereExternalId($value)
 * @method static Builder|LeadOrderAssignment whereFrxCallId($value)
 * @method static Builder|LeadOrderAssignment whereFrxLeadId($value)
 * @method static Builder|LeadOrderAssignment whereGenderId($value)
 * @method static Builder|LeadOrderAssignment whereId($value)
 * @method static Builder|LeadOrderAssignment whereIsLive($value)
 * @method static Builder|LeadOrderAssignment whereIsPayable($value)
 * @method static Builder|LeadOrderAssignment whereLeadId($value)
 * @method static Builder|LeadOrderAssignment whereProfession($value)
 * @method static Builder|LeadOrderAssignment whereRedirectUrl($value)
 * @method static Builder|LeadOrderAssignment whereRegisteredAt($value)
 * @method static Builder|LeadOrderAssignment whereRejectReason($value)
 * @method static Builder|LeadOrderAssignment whereReplaceAuthId($value)
 * @method static Builder|LeadOrderAssignment whereRouteId($value)
 * @method static Builder|LeadOrderAssignment whereSimulateAutologin($value)
 * @method static Builder|LeadOrderAssignment whereSmoothSort($value)
 * @method static Builder|LeadOrderAssignment whereStatus($value)
 * @method static Builder|LeadOrderAssignment whereTimezone($value)
 * @method static Builder|LeadOrderAssignment whereUpdatedAt($value)
 * @method static Builder|LeadOrderAssignment withDeliveryTry()
 * @method static Builder|LeadOrderAssignment withoutDeliveryFailed(?string $message = null)
 * @method static Builder|LeadOrderAssignment withoutDeliveryTry()
 * @mixin \Eloquent
 */
class LeadOrderAssignment extends Model
{
    use SaveQuietly;
    use RecordEvents;

    public const READ = 'read';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'created'  => LeadAssigned::class,
        'creating' => LeadAssigning::class,
        'saved'    => AssignmentSaved::class,
        'updating' => AssignmentUpdating::class,
        'deleted'  => AssignmentDeleted::class,
    ];


    /**
     * Cast dates to carbon instances
     *
     * @var string[]
     */
    protected $dates = [
        'called_at',
        'callback_at',
        'registered_at',
        'deliver_at',
    ];

    /**
     * Format dates for JSON output
     *
     * @param \DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function route()
    {
        return $this->belongsTo(LeadOrderRoute::class, 'route_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function batch()
    {
        return $this->hasOneThrough(
            ResellBatch::class,
            LeadResellBatch::class,
            'assignment_id',
            'id',
            'id',
            'batch_id'
        );
    }

    /**
     * @return \App\Manager
     */
    public function getManager()
    {
        return optional(optional($this->route)->manager)->load('tenant');
    }

    /**
     * Get default sheet name
     *
     * @return string
     */
    public function getDestinationSheetName()
    {
        if ($this->route->offer->name === 'TINKOFF_BONUS2_TZ') {
            return sprintf(
                '%s-%s',
                now()->monthName,
                'TINKOFF_MARKIZ',
            );
        }

        return sprintf(
            '%s-%s',
            now()->monthName,
            $this->route->offer->name,
        );
    }

    /**
     * Push lead to destination sheet
     *
     * @return \Google_Service_Sheets_AppendValuesResponse
     */
    public function pushLead()
    {
        logger(sprintf('Pushing: %s', $this->id), ['gsheets']);
        try {
            $payload = $this->getManager()
                ->spreadsheet()
                ->appendLead($this->getDestinationSheetName(), $this);
        } catch (\Throwable $e) {
            logger(sprintf('Error pushing: %s. %s', $this->id, $e->getMessage()), ['gsheets']);

            throw $e;
        }

        $this->update(['confirmed_at' => now()]);

        return $payload;
    }

    /**
     * Determine is assignment completed succesfully
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed_at;
    }

    /**
     * Mark assignment as confirmed
     *
     * @return bool
     */
    public function markAsConfirmed()
    {
        NewLeadAssigned::dispatch($this);

        return $this->update([
            'confirmed_at' => now()
        ]);
    }

    /**
     * Scope assignments to visible
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (app()->runningInConsole()) {
            return $builder;
        }

        if (auth('web')->check() || auth('api')->check()) {
            if (auth()->id() === 230) {
                return $builder->where('lead_order_assignments.created_at', '<', '2021-11-05 00:00:00');
            }

            if (auth()->user()->isSupport()) {
                return $builder->whereHas('lead', function ($leadQuery) {
                    return $leadQuery->visible();
                });
            }

            if (auth()->user()->hasRole([User::HEAD, User::TEAMLEAD])) {
                return $builder->whereHas('lead', function ($leadQuery) {
                    return $leadQuery->visible();
                });
            }

            return $builder;
        }

        if (auth('crm')->id() === 3761) {
            return $builder->whereHas('route', function ($query) {
                return $query->whereIn('manager_id', Manager::whereIn('office_id', [8,20,25,83,108,118])->pluck('managers.id'));
            });
        }

        if (! auth('crm')->user()->hasTenant()) {
            if (auth('crm')->user()->isAdmin() && auth('crm')->user()->office_id !== null) {
                return $builder->whereHas('route', function ($query) {
                    return $query->whereIn('order_id', auth('crm')->user()->office->orders()->pluck('id'));
                });
            }

            return $builder->whereHas('route', function ($query) {
                return $query->where('manager_id', auth('crm')->id());
            });
        }

        if (auth('crm')->user()->isOfficeHead() && auth('crm')->user()->office_id !== null) {
            return $builder->whereHas('route', function ($query) {
                return $query->whereIn('manager_id', auth('crm')->user()->office->managers()->pluck('id'));
            });
        }

        if (auth('crm')->user()->isRoot() && auth('crm')->user()->frx_tenant_id !== null) {
            return $builder->whereHas('route', function ($query) {
                return $query->whereIn('manager_id', auth('crm')->user()->tenant->managers()->pluck('id'));
            });
        }

        return $builder->whereHas('route', function ($query) {
            return $query->where('manager_id', auth('crm')->id());
        });
    }

    /**
     * Filter assignments by offer
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param null                                  $offer
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForOffer(Builder $builder, $offer = null)
    {
        if ($offer === null) {
            return $builder;
        }

        return $builder->whereIn('route_id', LeadOrderRoute::where('offer_id', $offer)->value('id'));
    }

    public function transfer(Manager $manager)
    {
        if ($manager->id == $this->route->manager_id) {
            return;
        }

        DB::beginTransaction();

        try {
            $oldRoute = $this->route;
            /** @var LeadOrderRoute $newRoute */
            $newRoute = $this->route->order->routes()->withTrashed()->firstOrCreate(
                ['manager_id' => $manager->id, 'offer_id' => $oldRoute->offer_id],
                ['status' => $oldRoute->status],
            );

            if ($newRoute->trashed()) {
                $newRoute->restore();
            }

            $newRoute->increment('leadsOrdered');
            $newRoute->increment('leadsReceived');

            $this->route()->decrement('leadsOrdered');
            $this->route()->decrement('leadsReceived');

            $this->route()->associate($newRoute);
            $this->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        $this->lead->addEvent(
            Lead::TRANSFERRED,
            [
                'manager_id' => $newRoute->manager_id,
                'route_id'   => $newRoute->id,
            ],
            [
                'manager_id' => $oldRoute->manager_id,
                'route_id'   => $oldRoute->id,
            ]
        );

        AssignmentTransferred::dispatch($this, $oldRoute, $newRoute);
    }

    /**
     * Get destination point of assignment
     *
     * @return \App\LeadDestination
     */
    public function getTargetDestination()
    {
        if ($this->route->hasCustomDestination()) {
            return $this->route->destination;
        }

        if ($this->getLeadsOrder()->hasCustomDestination()) {
            return $this->getLeadsOrder()->destination;
        }

        return $this->getLeadsOrder()->office->destination ?? LeadDestination::default();
    }

    /**
     * @return \App\LeadsOrder
     */
    public function getLeadsOrder()
    {
        return $this->route->order;
    }

    /**
     *  Record DestinationId
     */
    public function recordDestinationId()
    {
        return tap($this)->update([
            'destination_id' => $this->getTargetDestination()->id,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destination()
    {
        return $this->belongsTo(LeadDestination::class, 'destination_id');
    }

    public function remove()
    {
        if ($this->isConfirmed()) {
            trail('User '.optional(auth()->user())->name.' deleting confirmed assignment '.$this->id.' #wtf');
        }

        DB::transaction(function () {
            $this->delete();

            $this->lead->addEvent(
                Lead::UNASSIGNED,
                [],
                [
                    'lead_id'  => $this->lead_id,
                    'route_id' => $this->route_id,
                ],
            );

            $this->route->update([
                'leadsReceived'    => $this->route->assignments()->count(),
                'last_received_at' => $this->route->assignments()->latest('created_at')->value('created_at')
            ]);
        });

        $result = Result::whereDate('date', $this->created_at)
            ->where('offer_id', $this->route->offer_id)
            ->where('office_id', $this->route->order->office_id)
            ->first();
        if ($result !== null) {
            $result->updateLeadsCount();
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeConfirmed(Builder $builder)
    {
        $builder->whereNotNull('confirmed_at');
    }

    /**
     * Determine if the assignment has deposit
     *
     * @return bool
     */
    public function hasDeposit(): bool
    {
        return $this->lead->deposits()
            ->whereDate('lead_return_date', $this->created_at)
            ->where('office_id', $this->route->order->office_id)
            ->exists();
    }

    /**
     * @return Deposit|null
     */
    public function getDeposit(): ?Deposit
    {
        /** @var Deposit $deposit */
        $deposit = $this->lead->deposits()
            ->whereDate('lead_return_date', $this->created_at)
            ->where('office_id', $this->route->order->office_id)
            ->first();

        return $deposit;
    }

    /**
     * Set benefit amount on lead
     *
     * @param float|null $benefit
     */
    public function updateBenefit(float $benefit = null)
    {
        $benefit = $benefit ?? LeadPaymentCondition::getCpl($this->route->offer_id, $this->route->order->office_id);

        $this->benefit = $benefit ?? 0.00;

        $this->saveQuietly();
    }

    /**
     * Shortcut to dispatch delivery job
     *
     * @return void
     */
    public function deliver()
    {
        DeliverAssignment::dispatchNow($this);
    }

    /**
     * Format phone number for the managers
     *
     * @return string
     */
    public function getFormattedAltPhoneAttribute()
    {
        return (mb_substr($this->alt_phone, 0, 1) == 8)
            ? substr_replace($this->alt_phone, 7, 0, 1) :
            $this->alt_phone;
    }

    /**
     * Determine is lead has alternative
     * phone number
     *
     * @return bool
     */
    public function hasAltPhone()
    {
        return $this->alt_phone !== null;
    }

    /**
     * Format alt phone
     *
     * @param $value
     */
    public function setAltPhoneAttribute($value)
    {
        $this->attributes['alt_phone'] = digits($value);
    }

    /**
     * Get formatted gender attribute
     *
     * @return mixed
     */
    public function getGenderAttribute()
    {
        return Lead::GENDERS[$this->gender_id] ?? 0;
    }

    /**
     * Determines if the lead assigned with autologin
     *
     * @return bool
     */
    public function getAutologinAttribute()
    {
        return !empty($this->redirect_url) && $this->is_live === true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labels()
    {
        return $this->belongsToMany(Label::class, null, 'assignment_id');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        if (
            auth()->user() instanceof User &&
            !auth()->user()->isAdmin() && !auth()->user()->isSupport() && !auth()->user()->isSubSupport()
        ) {
            $builder->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                return $query->select(DB::raw('1'))
                    ->from('lead_order_routes')
                    ->whereColumn('lead_order_assignments.route_id', 'lead_order_routes.id')
                    ->whereIn('lead_order_routes.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
            });
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePayable(Builder $builder)
    {
        return $builder->where('is_payable', true);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePendingDelayed(Builder $builder)
    {
        return $builder->whereNotNull('deliver_at')
            ->where('deliver_at', '>', now()->seconds(0))
            ->whereNull('confirmed_at')
            ->whereNull('delivery_failed');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithDeliveryTry(Builder $builder)
    {
        return $builder->whereHas('lead.events', function ($query) {
            return $query->where('type', Lead::DELIVERY_TRY)
                ->where(
                    DB::raw('custom_data::text'),
                    'like',
                    DB::raw("concat('%\"assignment_id\":', lead_order_assignments.id,',%')")
                );
        });
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithoutDeliveryTry(Builder $builder)
    {
        return $builder->whereDoesntHave('lead.events', function ($query) {
            return $query->where('type', Lead::DELIVERY_TRY)
                ->where(
                    DB::raw('custom_data::text'),
                    'like',
                    DB::raw("concat('%\"assignment_id\":', lead_order_assignments.id,',%')")
                );
        });
    }

    /**
     * @param Builder     $builder
     * @param string|null $message
     *
     * @return Builder
     */
    public function scopeWithoutDeliveryFailed(Builder $builder, ?string $message = null)
    {
        return $builder->whereDoesntHave('lead.events', function ($query) use ($message) {
            return $query->where('type', Lead::DELIVERY_FAILED)
                ->where(
                    DB::raw('custom_data::text'),
                    'like',
                    DB::raw("concat('%\"assignment_id\":', lead_order_assignments.id,',%')")
                )
                ->when($message, fn ($q) => $q->where(DB::raw('custom_data::text'), 'like', "%$message%"));
        });
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeUndelivered(Builder $builder)
    {
        return $builder->whereNull('confirmed_at')
            ->whereNotNull('delivery_failed');
    }

    /**
     * @return void
     */
    public function markAsUnpayable()
    {
        $this->update([
            'is_payable' => false,
            'benefit'    => 0,
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function markAsLeftover()
    {
        if (!$this->route->offer->isLeftover()) {
            $offer = Offer::firstOrCreate(['name' => sprintf('LO_%s', $this->route->offer->name)]);

            $this->changeOffer($offer);
        }
    }

    /**
     * @param Offer $targetOffer
     *
     * @throws \Throwable
     */
    public function changeOffer(Offer $targetOffer)
    {
        if ($targetOffer->id === $this->route->offer_id) {
            return;
        }

        DB::transaction(function () use ($targetOffer) {
            /** @var LeadOrderRoute $newRoute */
            $newRoute = $this->route->order->routes()->withTrashed()->firstOrCreate(
                [
                    'manager_id' => $this->route->manager_id,
                    'offer_id'   => $targetOffer->id
                ],
                [
                    'status'         => $this->route->status,
                    'destination_id' => $this->route->destination_id,
                ],
            );

            if ($newRoute->trashed()) {
                $newRoute->restore();
            }

            $newRoute->increment('leadsOrdered');
            $newRoute->increment('leadsReceived');

            $this->route()->decrement('leadsOrdered');
            $this->route()->decrement('leadsReceived');

            $this->route()->associate($newRoute);
            $this->save();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function callbacks()
    {
        return $this->hasMany(Callback::class, 'assignment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scheduledCallbacks()
    {
        return $this->callbacks()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->scheduled();
    }

    /**
     * @return Callback
     */
    public function actualCallback(): Callback
    {
        return $this->scheduledCallbacks()
            ->incomplete()
            ->firstOrNew(
                [],
                [
                    'phone' => $this->lead->formatted_phone,
                ]
            );
    }

//    public function getCallbackAtAttribute(): ?\Illuminate\Support\Carbon
//    {
//        return $this->callback_at ?? optional($this->scheduledCallbacks->first())->call_at;
//    }

    /**
     * @return \Illuminate\Support\Carbon|null
     */
    public function getActualTimeAttribute(): ?\Illuminate\Support\Carbon
    {
        if (!isset($this->attributes['actual_time'])) {
            if (Timezone::where('name', $this->timezone)->exists()) {
                $mscDiff = (int)preg_replace('~[^0-9\-+]~', '', $this->timezone);

                $this->attributes['actual_time'] = now($mscDiff + now('Europe/Moscow')->utcOffset() / 60);
            } elseif ($this->registration_timezone !== null) {
                $this->attributes['actual_time'] = now($this->registration_timezone);
            } else {
                $this->attributes['actual_time'] = null;
            }
        }

        return $this->attributes['actual_time'];
    }

    /**
     * @return string|null
     */
    public function getActualBenefitAttribute()
    {
        $cpa = LeadPaymentCondition::getCpa($this->route->offer_id, $this->route->order->office_id);

        if ($cpa !== null) {
            return optional($this->getDeposit())->benefit;
        }

        return $this->benefit;
    }

    /**
     * Determines is assignment was delivered
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->delivery_failed === null;
    }

    /**
     * @return bool
     */
    public function hasDeliveryTry(): bool
    {
        return $this->lead->events()
            ->where('type', Lead::DELIVERY_TRY)
            ->where(DB::raw('custom_data::text'), 'like', sprintf('%%"assignment_id":%s,%%', $this->id))
            ->exists();
    }

    public function createDeposit(CallResult $result)
    {
        return $this->lead->deposits()->create([
            'lead_return_date' => $this->created_at,
            'date'             => $result->getDepositDate(),
            'sum'              => $result->getDepositSum(),
            'phone'            => $this->lead->phone,
            'account_id'       => $this->lead->account_id,
            'user_id'          => $this->lead->user_id,
            'office_id'        => $this->route->order->office_id,
            'offer_id'         => $this->lead->offer_id,
        ]);
    }
}
