<?php

namespace App\Facebook\Traits;

use App\Issue;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

/**
 * Trait to deal with various issues
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasIssues
{
    /**
     * Get issues related to model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function issues()
    {
        return $this->morphMany(Issue::class, 'issuable');
    }

    /**
     * Remove issue(s)
     *
     * @param \App\Issue|null $issue
     *
     * @return bool
     */
    public function clearIssue(?Issue $issue = null)
    {
        if ($issue === null) {
            return $this->issues()->pending()->each(function (Issue $issue) {
                $issue->clear();
            });
        }

        return $issue->clear();
    }

    /**
     * Attach new issue to model
     *
     * @param \Throwable $throwable
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addIssue(Throwable $throwable)
    {
        if (! $this->isDuplicate($throwable)) {
            return $this->issues()->create([
                'message' => $throwable->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Determine is given model has any running issues
     *
     * @return bool
     */
    public function hasIssues(): bool
    {
        return $this->issues()->pending()->exists() > 0 || $this->hasNotToken();
    }

    /**
     * Get attribute for `appends` in models
     *
     * @return bool
     */
    public function getHasIssuesAttribute()
    {
        return $this->hasIssues();
    }

    /**
     * Append last issue text
     *
     * @return string|null
     */
    public function getLastIssueAttribute()
    {
        return optional($this->issues()->pending()->first())->message;
    }

    /**
     * Determine is model has the same pending issue
     *
     * @param \Throwable $throwable
     *
     * @return mixed
     */
    private function isDuplicate(Throwable $throwable)
    {
        return $this->issues()->pending()->where('message', $throwable->getMessage())->exists();
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithoutIssues(Builder $builder)
    {
        return $builder->whereNotExists(function ($q) {
            $q->from('issues')
                ->where('issuable_type', static::class)
                ->whereRaw('"' . $this->getTable() . '"."id" = "issues"."issuable_id"::INTEGER')
                ->whereNull('fixed_at');
        });
    }
}
