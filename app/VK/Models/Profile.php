<?php

namespace App\VK\Models;

use App\AdsBoard;
use App\User;
use App\VK\Events\Profiles\Creating;
use App\VK\Jobs\CollectForms;
use App\VK\Jobs\CollectGroups;
use App\VK\Jobs\CollectLeads;
use App\VK\Jobs\RefreshProfileInformation;
use App\VK\VKApp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\VK\Models\Profile
 *
 * @property int $id
 * @property string $name
 * @property string $vk_id
 * @property string $token
 * @property int $user_id
 * @property array|null $issues_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\VK\Models\Form[] $forms
 * @property-read int|null $forms_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\VK\Models\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read User $user
 *
 * @method static Builder|Profile newModelQuery()
 * @method static Builder|Profile newQuery()
 * @method static Builder|Profile query()
 * @method static Builder|Profile visible()
 * @method static Builder|Profile whereCreatedAt($value)
 * @method static Builder|Profile whereId($value)
 * @method static Builder|Profile whereIssuesInfo($value)
 * @method static Builder|Profile whereName($value)
 * @method static Builder|Profile whereToken($value)
 * @method static Builder|Profile whereUpdatedAt($value)
 * @method static Builder|Profile whereUserId($value)
 * @method static Builder|Profile whereVkId($value)
 * @method static Builder|Profile withoutIssues()
 * @mixin \Eloquent
 */
class Profile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vk_profiles';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['token'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issues_info' => 'json',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => Creating::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function forms()
    {
        return $this->hasManyThrough(
            Form::class,
            Group::class,
            'profile_id',
            'vk_group_id',
            'id',
            'vk_id'
        );
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->check() && auth()->user()->role == User::BUYER) {
            return $builder->where('user_id', auth()->id())->orWhere(function (Builder $query) {
                return $query->where('user_id', null)->where('created_at', '>', now()->subMonth()->toDateTimeString());
            });
        }

        if (auth()->check() && auth()->user()->isVerifier()) {
            return $builder->where('user_id', auth()->id());
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     */
    public function scopeWithoutIssues(Builder $builder)
    {
        return $builder->whereNull('vk_profiles.issues_info');
    }

    /**
     * Load fresh data from the VK API
     *
     * @param bool $force
     *
     * @return void
     */
    public function refreshVKData(bool $force = false)
    {
        RefreshProfileInformation::withChain([
            new CollectGroups($this, $force),
            new CollectForms($this, $force),
            new CollectLeads($this, $force),
        ])
            ->dispatch($this, $force)->allOnQueue(AdsBoard::QUEUE_VK);
    }

    /**
     * @param \Throwable $error
     *
     * @return $this
     */
    public function addError(\Throwable $error)
    {
        $this->update([
            'issues_info' => [
                'message' => $error->getMessage(),
                'code'    => $error->getCode(),
            ]
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    public function clearErrors()
    {
        $this->update([
            'issues_info' => null
        ]);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return isset($this->issues_info['message']);
    }

    /**
     * @return VKApp
     */
    public function initVKApp(): VKApp
    {
        return app(VKApp::class)->useToken($this->token);
    }
}
