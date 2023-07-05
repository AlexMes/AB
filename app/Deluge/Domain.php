<?php

namespace App\Deluge;

use App\Deluge\Notifications\DomainIsDown;
use App\Deluge\Notifications\DomainRecovered;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * App\Deluge\Domain
 *
 * @property int $id
 * @property string $url
 * @property string|null $destination
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $last_checked_at
 * @property \Illuminate\Support\Carbon|null $down_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read void $status
 * @property-read User $user
 *
 * @method static Builder|Domain newModelQuery()
 * @method static Builder|Domain newQuery()
 * @method static Builder|Domain query()
 * @method static Builder|Domain visible()
 * @method static Builder|Domain whereCreatedAt($value)
 * @method static Builder|Domain whereDestination($value)
 * @method static Builder|Domain whereDownAt($value)
 * @method static Builder|Domain whereId($value)
 * @method static Builder|Domain whereLastCheckedAt($value)
 * @method static Builder|Domain whereUpdatedAt($value)
 * @method static Builder|Domain whereUrl($value)
 * @method static Builder|Domain whereUserId($value)
 * @mixin \Eloquent
 */
class Domain extends Model
{
    use HasEvents;

    protected $table = 'deluge_domains';

    protected $guarded = [];

    protected $dates = [
        'created_at','down_at','last_checked_at','updated_at',
    ];

    /**
     * Limit domains to visible only
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (app()->runningInConsole() || auth()->user()->isAdmin()) {
            return $builder;
        }

        if (auth()->user()->isBranchHead()) {
            return $builder->whereIn('user_id', User::visible()->pluck('id'));
        }

        return $builder->where('user_id', auth()->id());
    }

    /**
     * Owner
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get doamin state
     *
     * @return void
     */
    public function getStatusAttribute()
    {
        return $this->down_at ? sprintf('Down since '.$this->down_at) : 'Active';
    }

    /**
     * Check that domain alive
     *
     * @return void
     */
    public function check()
    {
        $this->update(['last_checked_at' => now()]);

        try {
            $response = Http::retry(2, 1)->timeout(3)->get($this->url)->throw();
            if ($response->successful() && $this->down_at !== null) {
                $this->update([
                    'down_at' => null,
                ]);
                $this->user->notify(new DomainRecovered($this));
            }
        } catch (\Throwable $th) {
            if ($this->down_at === null) {
                $this->update(['down_at' => now()]);
                optional($this->user)->notify(new DomainIsDown($this));
            }
        }
    }
}
