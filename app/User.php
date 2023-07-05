<?php

namespace App;

use App\Facebook\Account;
use App\Facebook\Profile;
use App\Facebook\ProfilePage;
use App\Traits\AppendAccessAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $role
 * @property string|null $telegram_id
 * @property string|null $binomTag
 * @property int|null $office_id
 * @property bool $showFbFields
 * @property int|null $report_sort
 * @property int|null $branch_id
 * @property string|null $google_tfa_secret
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Access[] $accesses
 * @property-read int|null $accesses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Account[] $accounts
 * @property-read int|null $accounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Branch[] $allowedBranches
 * @property-read int|null $allowed_branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Offer[] $allowedOffers
 * @property-read int|null $allowed_offers_count
 * @property-read \App\Branch|null $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\NotificationType[] $deniedTelegramNotifications
 * @property-read int|null $denied_telegram_notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Deposit[] $deposits
 * @property-read int|null $deposits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain[] $domains
 * @property-read int|null $domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Firewall[] $firewall
 * @property-read int|null $firewall_count
 * @property-read string $avatar
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Office|null $office
 * @property-read \Illuminate\Database\Eloquent\Collection|ProfilePage[] $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Profile[] $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $sharedUsers
 * @property-read int|null $shared_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User forAllowedBranches()
 * @method static \Illuminate\Database\Eloquent\Builder|User forBranchStats()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User teammates($userId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User visible()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBinomTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogleTfaSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReportSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShowFbFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withFacebookTraffic()
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    use SoftDeletes;
    use AppendAccessAttributes;

    /** @var string  */
    public const ADMIN = 'admin';

    /** @var string  */
    public const BUYER = 'buyer';

    /** @var string  */
    public const CUSTOMER = 'customer';

    /** @var string  */
    public const FARMER = 'farmer';

    /** @var string  */
    public const FINANCIER = 'financier';

    /** @var string  */
    public const DESIGNER = 'designer';

    /** @var string  */
    public const TEAMLEAD = 'teamlead';

    /** @var string  */
    public const VERIFIER = 'verifier';

    /** @var string  */
    public const GAMBLER      = 'gambler';

    /** @var string  */
    public const GAMBLE_ADMIN = 'gamble-admin';

    /** @var string  */
    public const HEAD = 'head';

    /** @var string  */
    public const SUPPORT = 'support';

    /** @var string  */
    public const DEVELOPER = 'developer';

    /** @var string  */
    public const SUBSUPPORT = 'subsupport';

    /** @var string  */
    public const SALES = 'sales';

    public const ROLES = [
        User::ADMIN,
        User::BUYER,
        User::CUSTOMER,
        User::FARMER,
        User::FINANCIER,
        User::DESIGNER,
        User::TEAMLEAD,
        User::VERIFIER,
        User::GAMBLER,
        User::GAMBLE_ADMIN,
        User::HEAD,
        User::SUPPORT,
        User::DEVELOPER,
        User::SUBSUPPORT,
        User::SALES,
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google_tfa_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Define default count of users per page
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * @var string|null
     */
    protected ?string $actAs = null;

    /**
     * Hash passwords by default
     *
     * @param string $password
     *
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] =  Hash::make($password);
    }

    /**
     * Get user's avatar
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        return sprintf("https://eu.ui-avatars.com/api/?name=%s&background=2C7A7B&color=F7FAFC", $this->name);
    }

    /**
     * Determine is certain user have boosted privileges
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === static::ADMIN;
    }

    /**
     * Determine is certain user are buyer
     *
     * @return bool
     */
    public function isBuyer(): bool
    {
        return $this->role === static::BUYER;
    }

    /**
     * Assigned profiles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Assigned domains
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * All assigned ads accounts for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function accounts()
    {
        return $this->hasManyThrough(Account::class, Profile::class);
    }

    /**
     * Determine is user customer
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->role === static::CUSTOMER;
    }

    /**
     * Scope results to allowed only
     *
     * @param \Illuminate\Database\Query\Builder $builder
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeVisible($builder)
    {
        if (! auth()->check() && app()->runningInConsole()) {
            return $builder;
        }

        if (auth()->id() === 230) {
            return $builder->where('users.created_at', '<', '2021-11-05 00:00:00');
        }

        if (auth()->user()->hasRole([static::HEAD,self::SUPPORT])) {
            return $builder->withTrashed()->where('users.branch_id', auth()->user()->branch_id);
        }

        if (auth()->user()->hasRole([static::TEAMLEAD, static::DESIGNER])) {
            return $builder->teammates();
        }

        if (auth()->user()->role === static::BUYER) {
            return $builder->whereIn('users.id', auth()->user()->sharedUsers()->pluck('users.id')->push(auth()->id()));
        }

        return $builder;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBranchStats(\Illuminate\Database\Eloquent\Builder $builder)
    {
        if (auth()->id() === 37) {
            return $builder->visible();
        }

        if (optional(auth()->user()->branch)->stats_access) {
            if (auth()->user()->isBuyer() || auth()->user()->isTeamLead() || auth()->user()->isBranchHead()) {
                return $builder->where('users.branch_id', auth()->user()->branch_id);
            }
        }

        return $builder->visible();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class)
            ->withDefault(fn () => new Office(['id' => null, 'name' => 'Internal']));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deniedTelegramNotifications()
    {
        return $this->belongsToMany(
            NotificationType::class,
            'denied_telegram_notification_user'
        );
    }

    /**
     * Determine whether the user has a notification by key
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasTelegramNotification($key)
    {
        return !$this->deniedTelegramNotifications()->where('key', $key)->exists();
    }

    /**
     * Determine is user farmer
     *
     * @return bool
     */
    public function isFarmer()
    {
        return $this->role === static::FARMER;
    }

    /**
     * Get visible offers for the user
     *
     * @return \App\Offer[]|\Illuminate\Database\Eloquent\Collection
     */
    public function offers()
    {
        return Cache::remember(sprintf('crm-user-offers-%s', $this->id), now()->addHours(5), function () {
            return Offer::all();
        });
    }

    /**
     * Determine is user allowed to access CRM
     *
     * @return bool
     */
    public function allowedToSeeCrm()
    {
        return $this->hasRole([static::ADMIN, static::SUPPORT, static::HEAD]);
    }

    /**
     * Determine if the user is allowed to access Deluge
     *
     * @return bool
     */
    public function allowedToSeeDeluge()
    {
        return $this->isAdmin()
                || $this->isBuyer()
                || $this->isTeamLead()
                || $this->role === static::HEAD
                || $this->isDeveloper()
                || $this->isDesigner()
                || $this->isFinancier()
                || $this->id === 89;
    }

    /**
     * Determine if the user is allowed to access AdsBoard
     *
     * @return bool
     */
    public function allowedToSeeBoard()
    {
        /// temp setup to check integration
        if (in_array($this->id, [218])) {
            return true;
        }

        return $this->isAdmin()
            || $this->isSupport()
            || $this->isDeveloper()
            || $this->isSubSupport()
            || $this->isVerifier()
            || $this->isBranchHead()
            || $this->isTeamLead();
    }

    /**
     * Determine if the user is allowed to access AdsBoard
     *
     * @return bool
     */
    public function allowedToSeeGamble()
    {
        return $this->isAdmin() || $this->isGambler() || $this->isGamblerAdmin();
    }

    /**
     * Deposits from user leads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * User leads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Facebook fan-pages attached to user profiles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function pages()
    {
        return $this->hasManyThrough(ProfilePage::class, Profile::class);
    }

    /**
     * Granted user accesses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accesses()
    {
        return $this->hasMany(Access::class);
    }

    /**
     * Gets user's permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function firewall()
    {
        return $this->hasMany(Firewall::class);
    }

    public function isFinancier()
    {
        return $this->role === static::FINANCIER;
    }

    public function isDesigner()
    {
        return $this->role === static::DESIGNER;
    }

    /**
     * @return bool
     */
    public function isOfficeHead()
    {
        return false;
    }

    public function isBranchHead()
    {
        return $this->role === static::HEAD;
    }

    /**
     * User's teams
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * Determines is user have any team
     *
     * @return bool
     */
    public function hasTeams()
    {
        return $this->teams()->exists();
    }

    /**
     * Determine is given user in team
     *
     * @param \App\Team|null $team
     *
     * @return bool
     */
    public function inTeam(?Team $team): bool
    {
        return $this->teams->contains($team);
    }

    /**
     * Adds user to the team
     *
     * @param \App\Team $team
     *
     * @return void
     */
    public function joinTeam(Team $team)
    {
        $this->teams()->attach($team);
    }

    /**
     * Remove user from the team
     *
     * @param \App\Team $team
     *
     * @return void
     */
    public function leaveTeam(Team $team)
    {
        $this->teams()->detach($team);
    }

    /**
     * Gets only teammates
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int|null                              $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTeammates($builder, $userId = null)
    {
        $user = static::find($userId = $userId ?? auth()->id());

        return $builder->whereHas('teams', fn ($teamQuery) => $teamQuery->whereIn('team_user.team_id', cache()->remember('teams-'.$user->id, now()->addHour(), fn () => $user->teams()->pluck('teams.id'))));
    }

    /**
     * Determine if certain user is teamlead
     *
     * @return bool
     */
    public function isTeamLead()
    {
        return $this->role === static::TEAMLEAD;
    }

    /**
     * Determine if current user has another one in their teams
     *
     * @param mixed|null $userId
     *
     * @return false
     */
    public function inTeammates($userId = null)
    {
        return $this->teams->flatMap->users->pluck('id')->contains($userId);
    }

    /**
     * Determine if certain user is verifier
     *
     * @return bool
     */
    public function isVerifier()
    {
        return $this->role === static::VERIFIER;
    }

    /**
     * @param $builder
     *
     * @return mixed
     */
    public function scopeWithFacebookTraffic(\Illuminate\Database\Eloquent\Builder $builder)
    {
        return $builder->whereIn('role', [User::BUYER,User::TEAMLEAD,User::HEAD])
            ->orWhereIn('id', [2,5,10])
            ->orderBy('report_sort');
    }

    public function isRoot()
    {
        return false;
    }

    /**
     * Determine if certain user is gambler
     *
     * @return bool
     */
    public function isGambler()
    {
        return $this->role === static::GAMBLER;
    }

    /**
     * Determine if certain user is gambler-admin
     *
     * @return bool
     */
    public function isGamblerAdmin()
    {
        return $this->role === static::GAMBLE_ADMIN;
    }

    /**
     * Determine if certain user is support
     *
     * @return bool
     */
    public function isSupport()
    {
        return $this->role === static::SUPPORT;
    }

    /**
     * Determine if certain user is gambler-admin
     *
     * @return bool
     */
    public function isDeveloper()
    {
        return $this->role === static::DEVELOPER;
    }

    /**
     * User's branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allowedOffers()
    {
        return $this->belongsToMany(Offer::class, 'allowed_offers', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharedUsers()
    {
        return $this->belongsToMany(self::class, 'shared_users', 'user_id', 'shared_id');
    }

    /**
     * @param int    $size
     * @param string $encoding
     *
     * @return string
     */
    public function getGoogleTFAQRCode(int $size = 200, string $encoding = 'utf-8'): string
    {
        return (new \PragmaRX\Google2FAQRCode\Google2FA())->getQRCodeInline(
            config('app.name', 'AdsBoard'),
            $this->email,
            $this->google_tfa_secret,
            $size,
            $encoding
        );
    }

    /**
     * @return void
     */
    public function generateGoogleTfaSecret(): void
    {
        $this->update(['google_tfa_secret' =>  app('pragmarx.google2fa')->generateSecretKey(32)]);
    }

    /**
     * @param string|null $secret
     */
    public function setGoogleTfaSecretAttribute(?string $secret): void
    {
        $this->attributes['google_tfa_secret'] = $secret !== null ? encrypt($secret) : null;
    }

    /**
     * @return string|null
     */
    public function getGoogleTfaSecretAttribute(): ?string
    {
        return $this->attributes['google_tfa_secret'] !== null ? decrypt($this->attributes['google_tfa_secret']) : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allowedBranches()
    {
        return $this->belongsToMany(Branch::class, 'allowed_branches');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAllowedBranches(\Illuminate\Database\Eloquent\Builder $builder)
    {
        if (auth()->user()->isDesigner()) {
            return $builder->whereIn('users.branch_id', auth()->user()->allowedBranches->pluck('id'));
        }

        return $builder;
    }

    /**
     * Determine is user sub support
     *
     * @return bool
     */
    public function isSubSupport()
    {
        return $this->role === self::SUBSUPPORT;
    }

    /**
     * Determine if the user is sales
     *
     * @return bool
     */
    public function isSales()
    {
        return $this->role === self::SALES;
    }

    /**
     * Performs action as another role
     *
     * @param string   $role
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function performAs(string $role, \Closure $callback)
    {
        $this->actAs($role);
        $result = $callback();
        $this->actAs(null);

        return $result;
    }

    /**
     * @return string
     */
    public function getRoleAttribute()
    {
        return $this->actAs ?? $this->attributes['role'];
    }

    /**
     * @param string|null $role
     *
     * @return $this
     */
    public function actAs(?string $role)
    {
        if (in_array($role, self::ROLES) || $role === null) {
            $this->actAs = $role;
        }

        return $this;
    }

    /**
     * Determine when user has specific role
     *
     * @param string|array $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }

        return $this->role === $role;
    }

    /**
     * Toggle revenue visibility on reports
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return void
     */
    public function toggleRevenueVisibility()
    {
        if ($this->displayRevenue()) {
            Cache::set('hide_revenue:' . $this->id, true, now()->addDay());
        } else {
            Cache::forget('hide_revenue:' . $this->id);
        }
    }

    /**
     * Determine if the user should see revenue on reports
     *
     * @return bool
     */
    public function displayRevenue()
    {
        return Cache::missing('hide_revenue:' . $this->id);
    }
}
