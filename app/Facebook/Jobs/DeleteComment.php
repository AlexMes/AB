<?php

namespace App\Facebook\Jobs;

use App\AdsBoard;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteComment implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public $tries = 1;
    protected $comment;
    protected $token;
    protected $account_id;
    protected $page_id;
    protected $facebook;

    /**
     * DeleteComment constructor.
     *
     * @param $comment
     * @param $token
     * @param mixed $ad
     */
    public function __construct($comment, $ad)
    {
        $this->comment    = $comment;
        $this->token      = $ad->pageToken();
        $this->account_id = $ad->account_id;
        $this->page_id    = $ad->page_id;
        $this->facebook   = $ad->getFacebookClient();
    }

    /**
     * Delete a comment
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Deleting comment with id: ' . $this->comment['id']);
        Log::debug('Comment message: ' . $this->comment['message']);

        try {
            CacheComments::dispatch($this->comment, $this->account_id, $this->page_id)->onQueue(AdsBoard::QUEUE_CLEANING);
            $this->facebook->delete($this->comment['id'], [], $this->token);
            Log::debug(sprintf("Comment %s gone ", $this->comment['id']));
        } catch (Exception $exception) {
            Log::debug(sprintf("Failed to remove comment %s", $this->comment['id']));
            Log::debug($exception->getMessage());
        }
    }
}
