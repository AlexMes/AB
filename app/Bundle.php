<?php

namespace App;

use App\Traits\AppendAccessAttributes;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * App\Bundle
 *
 * @property int $id
 * @property int|null $offer_id
 * @property string|null $utm_campaign
 * @property string|null $examples
 * @property string|null $geo
 * @property int|null $age
 * @property string|null $gender
 * @property string|null $interests
 * @property string|null $device
 * @property string|null $platform
 * @property string|null $prelend_link
 * @property string|null $lend_link
 * @property string|null $utm_source
 * @property string|null $utm_content
 * @property string|null $utm_term
 * @property string|null $utm_medium
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $ad
 * @property string|null $title
 * @property string|null $description
 * @property string|null $text
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Offer|null $offer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Placement[] $placements
 * @property-read int|null $placements_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereAd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereExamples($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereGeo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereLendLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle wherePrelendLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereUtmCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereUtmContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereUtmMedium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereUtmSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereUtmTerm($value)
 * @mixin \Eloquent
 */
class Bundle extends Model implements HasMedia
{
    use AppendAccessAttributes,
        HasMediaTrait;

    protected $guarded = ['id'];

    protected $appends = ['can_update'];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get all comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function placements()
    {
        return $this->belongsToMany(Placement::class);
    }
}
