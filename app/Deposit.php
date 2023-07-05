<?php

namespace App;

use App\Events\Deposits\Created;
use App\Events\Deposits\Saved;
use App\Events\Deposits\Updated;
use App\Facebook\Account;
use App\Traits\AppendAccessAttributes;
use App\Traits\HasVisibilityScope;
use App\Traits\RecordEvents;
use App\Traits\SaveQuietly;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Deposit
 *
 * @property int $id
 * @property string|null $lead_return_date
 * @property string|null $date
 * @property int|null $sum
 * @property string|null $phone
 * @property string|null $account_id
 * @property int|null $user_id
 * @property int|null $office_id
 * @property int|null $offer_id
 * @property int|null $lead_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $benefit
 * @property-read Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 * @property-read \App\Lead|null $lead
 * @property-read \App\Offer|null $offer
 * @property-read \App\Office|null $office
 * @property-read \App\User|null $user
 *
 * @method static Builder|Deposit allowedOffers()
 * @method static Builder|Deposit newModelQuery()
 * @method static Builder|Deposit newQuery()
 * @method static Builder|Deposit query()
 * @method static Builder|Deposit visible()
 * @method static Builder|Deposit whereAccountId($value)
 * @method static Builder|Deposit whereBenefit($value)
 * @method static Builder|Deposit whereCreatedAt($value)
 * @method static Builder|Deposit whereDate($value)
 * @method static Builder|Deposit whereId($value)
 * @method static Builder|Deposit whereLeadId($value)
 * @method static Builder|Deposit whereLeadReturnDate($value)
 * @method static Builder|Deposit whereOfferId($value)
 * @method static Builder|Deposit whereOfficeId($value)
 * @method static Builder|Deposit wherePhone($value)
 * @method static Builder|Deposit whereSum($value)
 * @method static Builder|Deposit whereUpdatedAt($value)
 * @method static Builder|Deposit whereUserId($value)
 * @mixin \Eloquent
 */
class Deposit extends Model
{
    use AppendAccessAttributes;
    use HasVisibilityScope;
    use SaveQuietly;
    use RecordEvents;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'deposits';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Created::class,
        'updated' => Updated::class,
        'saved'   => Saved::class,
    ];

    /**
     * Number of items on single page
     *
     * @var int
     */
    protected $perPage = 25;

    /**
     * Append attributes on every query
     *
     * @var array
     */
    protected $appends = [
        'can_update',
        'can_delete'
    ];

    /**
     * Create new deposit based on lead data
     *
     * @param \App\LeadOrderAssignment $assignment
     *
     * @return bool|\Illuminate\Database\Eloquent\Model|int
     */
    public static function createFromAssignment(LeadOrderAssignment $assignment)
    {
        if ($assignment->hasDeposit()) {
            return tap($assignment->getDeposit())->update([
                'sum' => $assignment->deposit_sum ?? 0
            ]);
        }

        return $assignment->lead->deposits()->create([
            'lead_return_date' => $assignment->created_at,
            'date'             => now()->toDateString(),
            'sum'              => $assignment->deposit_sum ?? 0,
            'phone'            => $assignment->lead->phone,
            'account_id'       => $assignment->lead->account_id,
            'user_id'          => $assignment->lead->user_id,
            'office_id'        => optional($assignment->getManager())->office_id ?? $assignment->route->order->office_id,
            'offer_id'         => $assignment->lead->offer_id,
        ]);
    }

    /**
     * @return LeadOrderAssignment|Builder|Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getAssignment()
    {
        return LeadOrderAssignment::whereLeadId($this->lead_id)
            ->whereDate('created_at', $this->lead_return_date)
            ->whereHas('route.order', fn ($query) => $query->where('office_id', $this->office_id))
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Scope deposits to visible only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('deposits.date', '<', '2021-11-05 00:00:00');
        }
        // if (auth()->id() === 89) {
        //     return $builder->whereDate('deposits.created_at', '>=', now()->subMonths(6)->startOfMonth()->toDateString());
        // }

        // if (auth()->id() === 162) {
        //     return $builder->where(function ($query) {
        //         return $query->whereIn('deposits.user_id', User::visible()->pluck('id'))
        //             ->orWhereIn('deposits.offer_id', Offer::where('name', 'ilike', '%TESLA%')->pluck('id'));
        //     });
        // }


        if (auth()->user()->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT])) {
            return $builder->where(function (Builder $query) {
                return $query->whereIn('deposits.user_id', User::visible()->pluck('users.id'))->orWhereNull('deposits.user_id');
            })->whereIn('deposits.offer_id', Offer::allowed()->pluck('offers.id'));
        }

        if (! auth()->user()->isAdmin()) {
            return $builder->joinSub(User::visible()->select(['id','branch_id']), 'visible', 'deposits.user_id', '=', 'visible.id');
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        // if (in_array(auth()->id(), [89])) {
        //     return $builder->whereIn('deposits.offer_id', Offer::where('vertical', 'Burj')->pluck('id'));
        // }

        if (in_array(auth()->id(), [132])) {
            return $builder;
        }

        if (in_array(auth()->id(), [37])) {
            return $builder->whereIn('deposits.offer_id', Offer::where('vertical', 'Crypt')->pluck('id'));
        }

        if (
            auth()->user() instanceof User &&
            !auth()->user()->isAdmin() && !auth()->user()->isSupport() && !auth()->user()->isDeveloper() && !auth()->user()->isSubSupport()
        ) {
            $builder->whereIn('deposits.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
        }

        return $builder;
    }

    /**
     * Related user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Related account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    /**
     * Related office
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Related offer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Related lead
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Find lead from phone number
     *
     * @return \App\Lead|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getCorrespondingLead()
    {
        return Lead::query()
            ->where('phone', $this->phone)
            ->where('office_id', $this->office_id)
            ->first();
    }

    /**
     * Get the result, attached to this deposit
     *
     * @return \App\Result|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getCorrespondingResult()
    {
        return Result::query()
            ->where('date', $this->lead_return_date)
            ->where('offer_id', $this->offer_id)
            ->where('office_id', $this->office_id)
            ->first();
    }

    /**
     * Find related result, and update deposits count on it
     *
     * @return void
     */
    public function updateCorrespondingResult()
    {
        $result = $this->getCorrespondingResult();

        if ($result) {
            $result->refreshFtd();
        }
    }

    /**
     * Find related lead, and attach it
     *
     * @return void
     */
    public function updateLeadInformation()
    {
        $lead = Lead::where('phone', $this->phone)->first();

        if ($lead !== null && $this->lead_id === null) {
            $this->update([
                'lead_id'    => $lead->id,
                'account_id' => $lead->account_id === null ? null : sprintf('act_%s', $lead->account_id),
                'offer_id'   => $lead->offer_id,
                'user_id'    => $lead->user_id
            ]);
        }
    }


    /**
     * Set benefit amount on deposit
     *
     * @param float|null $benefit
     */
    public function updateBenefit(float $benefit = null)
    {
        $benefit = $benefit ?? LeadPaymentCondition::getCpa($this->offer_id, $this->office_id);

        $this->benefit = $benefit ?? 0.00;

        $this->saveQuietly();
    }


    public function setSumAttribute($value)
    {
        $this->attributes['sum'] = (int) $value;
    }
}
