<?php

namespace App\Facebook\Jobs;

use App\Facebook\Comment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CacheComments implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Comment to cache
     *
     * @var array
     */
    protected $comment;

    /**
     * Account id for comment
     *
     * @var string
     */
    protected $account;

    /**
     * Page id for comment
     *
     * @var string
     */
    protected $page;

    /**
     * CacheComments constructor.
     *
     * @param $comment
     * @param mixed $account
     * @param mixed $page
     */
    public function __construct($comment, $account, $page)
    {
        $this->comment  = $comment;
        $this->account  = $account;
        $this->page     = $page;
    }

    /**
     * Handle the job
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Caching comment with id: ' . $this->comment['id']);
        Log::debug('Cached comment message: ' . $this->comment['message']);
        try {
            Comment::updateOrCreate([
                'id'           => $this->comment['id'],
            ], [
                'account_id'   => $this->account,
                'page_id'      => $this->page,
                'published_at' => Carbon::parse($this->comment['created_time'])->toDateTimeString(),
                'text'         => $this->comment['message'],
                'deleted_at'   => now(),
            ]);
        } catch (\Exception $exception) {
            Log::debug(
                sprintf("Failed to cache comment from %s. %s", $this->comment['created_time'], $exception->getMessage())
            );
        }
    }
}
