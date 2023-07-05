<?php

namespace App\Facebook\Jobs;

use App\AdsBoard;
use App\Facebook\Ad;
use App\Facebook\SentComment;
use App\Group;
use App\StopWord;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class DeleteCommentsFromPosts
 *
 * @package App\Facebook\Jobs
 */
class FlushComments implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * Tries before failure
     *
     * @var int
     */
    public $tries = 1;

    /**
     * @var \App\Facebook\Ad
     */
    protected $ad;

    /**
     * DeleteCommentsFromPosts constructor.
     *
     * @param \App\Facebook\Ad $ad
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Process a job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->ad->hasPostId() && $this->notInDisableList()) {
            $this->flush();
        }
    }

    /**
     * Run flusher
     *
     * @return void
     */
    protected function flush()
    {
        $this->ad->comments()
            ->reject(fn ($comment) => SentComment::where('comment_id', $comment['id'])->exists())
            ->filter(fn ($comment) => Str::contains($comment['message'], $this->wordsList()))
            ->reject(fn ($comment) => $this->tooYoung($comment))
            ->each(fn ($comment)   => DeleteComment::dispatch($comment, $this->ad)->onQueue(AdsBoard::QUEUE_CLEANING));
    }

    /**
     * @return bool
     */
    protected function notInDisableList()
    {
        return Group::find(23)->accounts()->where('id', sprintf("act_%s", $this->ad->account_id))->doesntExist();
    }


    /**
     * Get array of stop-list for filter
     *
     * @return mixed
     */
    protected function wordsList()
    {
        return Cache::remember('fb-stop-words', now()->addHours(12), fn () => StopWord::pluck('keyword')->toArray());
    }

    /**
     * Determine is comment created less than 20 minutes ago
     *
     * @param $comment
     *
     * @return bool
     */
    protected function tooYoung($comment): bool
    {
        try {
            $diff = Carbon::parse($comment['created_time'])->diffInMinutes(now());

            return $diff >= 20;
        } catch (\Throwable $exception) {
            Log::error(sprintf("Caught error while diffing comment creation time. %s", $exception->getMessage()));

            return false;
        }
    }
}
