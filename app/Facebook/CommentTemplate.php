<?php

namespace App\Facebook;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Facebook\CommentTemplate
 *
 * @property int $id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CommentTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentTemplate whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommentTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommentTemplate extends Model
{
    protected $table = 'facebook_comments_templates';
}
