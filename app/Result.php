<?php

namespace App;

use App\Traits\AppendAccessAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Result
 *
 * @property int $id
 * @property mixed $date
 * @property int|null $offer_id
 * @property int|null $office_id
 * @property int|null $leads_count
 * @property int|null $no_answer_count
 * @property int|null $reject_count
 * @property int|null $wrong_answer_count
 * @property int|null $demo_count
 * @property int|null $ftd_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 * @property-read \App\Offer|null $offer
 * @property-read \App\Office|null $office
 *
 * @method static Builder|Result allowedOffers()
 * @method static Builder|Result newModelQuery()
 * @method static Builder|Result newQuery()
 * @method static \Illuminate\Database\Query\Builder|Result onlyTrashed()
 * @method static Builder|Result query()
 * @method static Builder|Result visible()
 * @method static Builder|Result whereCreatedAt($value)
 * @method static Builder|Result whereDate($value)
 * @method static Builder|Result whereDeletedAt($value)
 * @method static Builder|Result whereDemoCount($value)
 * @method static Builder|Result whereFtdCount($value)
 * @method static Builder|Result whereId($value)
 * @method static Builder|Result whereLeadsCount($value)
 * @method static Builder|Result whereNoAnswerCount($value)
 * @method static Builder|Result whereOfferId($value)
 * @method static Builder|Result whereOfficeId($value)
 * @method static Builder|Result whereRejectCount($value)
 * @method static Builder|Result whereUpdatedAt($value)
 * @method static Builder|Result whereWrongAnswerCount($value)
 * @method static \Illuminate\Database\Query\Builder|Result withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Result withoutTrashed()
 * @mixin \Eloquent
 */
class Result extends Model
{
    use AppendAccessAttributes;
    use SoftDeletes;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'results';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Cast attributes
     *
     * @var array
     */
    protected $casts = [
        'date'       => 'date:Y-m-d',
    ];

    /**
     * Append attributes to JSON
     *
     * @var array
     */
    protected $appends = [
        'can_update',
        'can_delete',
    ];

    /**
     * Number of items on page
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * Assigned office
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Assigned offer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Count deposits for result, and update
     * related column
     *
     * @return void
     */
    public function refreshFtd()
    {
        $this->update([
            'ftd_count' => Deposit::query()
                ->where('offer_id', $this->offer_id)
                ->where('office_id', $this->office_id)
                ->where('lead_return_date', $this->date)
                ->count(),
        ]);
    }

    public function updateLeadsCount()
    {
        if ($this->getOrder() !== null) {
            $leads = $this->getOrder()
                ->routes()
                ->withTrashed()
                ->where('offer_id', $this->offer_id)
                ->selectRaw(DB::raw('sum("leadsReceived") as count'))
                ->first();

            $this->update([
                'leads_count' => $leads->count,
            ]);
        }
    }

    public function getOrder()
    {
        return LeadsOrder::whereDate('date', $this->date)->where('office_id', $this->office_id)->first();
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        if (auth()->user() === 89) {
            return $builder->whereIn('results.offer_id', Offer::burj()->pluck('id'));
        }

        if (auth()->user() instanceof User && !auth()->user()->isAdmin()) {
            $builder->whereIn(
                'results.offer_id',
                auth()->user()->allowedOffers->pluck('id')->values()
            );
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('results.date', '<', '2021-11-05');
        }

        if (auth()->user()->isSupport()) {
            $builder->where('results.date', '>=', now()->startOfYear()->toDateString());
        }

        if (auth()->user()->isSubSupport()) {
            $builder->where('results.date', '>=', now()->subMonth()->startOfMonth()->toDateString());
        }

        if (auth()->user()->isSupport()) {
            $builder->where('results.date', '>=', now()->startOfYear()->toDateString());
        }

        return $builder;
    }
}
