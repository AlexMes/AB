<?php

namespace App\Gamble;

use App\Gamble\Scopes\GamblerScope;
use App\User as BaseUser;

/**
 * App\Gamble\User
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Account[] $accounts
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\ProfilePage[] $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Profile[] $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|BaseUser[] $sharedUsers
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
 * @mixin \Eloquent
 */
class User extends BaseUser
{
    /**
     * Scope queries to gamblers
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new GamblerScope());
    }
}
