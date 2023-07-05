<?php

namespace App;

use App\Events\Access\Saved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Access
 *
 * @property int $id
 * @property string $received_at
 * @property string|null $name
 * @property int|null $user_id
 * @property int|null $supplier_id
 * @property string $type
 * @property string|null $facebook_url
 * @property string|null $fbId
 * @property string|null $login
 * @property string|null $password
 * @property string|null $email
 * @property string|null $email_password
 * @property string|null $birthday
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $profile_name
 * @property-read \App\AccessSupplier|null $supplier
 * @property-read \App\User|null $user
 *
 * @method static Builder|Access newModelQuery()
 * @method static Builder|Access newQuery()
 * @method static Builder|Access query()
 * @method static Builder|Access visible()
 * @method static Builder|Access whereBirthday($value)
 * @method static Builder|Access whereCreatedAt($value)
 * @method static Builder|Access whereEmail($value)
 * @method static Builder|Access whereEmailPassword($value)
 * @method static Builder|Access whereFacebookUrl($value)
 * @method static Builder|Access whereFbId($value)
 * @method static Builder|Access whereId($value)
 * @method static Builder|Access whereLogin($value)
 * @method static Builder|Access whereName($value)
 * @method static Builder|Access wherePassword($value)
 * @method static Builder|Access whereProfileName($value)
 * @method static Builder|Access whereReceivedAt($value)
 * @method static Builder|Access whereSupplierId($value)
 * @method static Builder|Access whereType($value)
 * @method static Builder|Access whereUpdatedAt($value)
 * @method static Builder|Access whereUserId($value)
 * @mixin \Eloquent
 */
class Access extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'accesses';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Hide attributes from JSON
     *
     * @var array
     */
    protected $hidden = [
        'password', 'email_password'
    ];

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => Saved::class,
    ];

    /**
     * Encrypt account password
     *
     * @param string $value
     */
    public function setPasswordAttribute(string $value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    /**
     * Encrypt email password
     *
     * @param string $value
     */
    public function setEmailPasswordAttribute(?string $value)
    {
        $this->attributes['email_password'] = Crypt::encryptString($value);
    }

    /**
     * Scope accesses to visible only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->check() && auth()->user()->isBuyer()) {
            $builder->where('user_id', auth()->id());
        }
    }

    /**
     * Assigned user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Access supplier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(AccessSupplier::class, 'supplier_id');
    }

    /**
     * Reveal password to profile
     *
     * @return string
     */
    public function getPassword()
    {
        return Crypt::decryptString($this->password);
    }

    /**
     * Reveal password to email
     *
     * @return string
     */
    public function getEmailPassword()
    {
        return Crypt::decryptString($this->email_password);
    }
}
