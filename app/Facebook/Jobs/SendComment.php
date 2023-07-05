<?php

namespace App\Facebook\Jobs;

use App\Facebook\Ad;
use App\Facebook\CommentTemplate;
use App\Facebook\SentComment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendComment implements ShouldQueue
{
    use SerializesModels;
    use Dispatchable;
    use InteractsWithQueue;

    /**
     * @var \App\Facebook\Ad
     */
    protected Ad $ad;

    /**
     * @var \App\Facebook\CommentTemplate
     */
    protected CommentTemplate $comment;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 1;

    /**
     * SendComment constructor.
     *
     * @param \App\Facebook\Ad              $ad
     * @param \App\Facebook\CommentTemplate $comment
     */
    public function __construct(Ad $ad, CommentTemplate $comment)
    {
        $this->ad      = $ad;
        $this->comment = $comment;
    }

    /**
     * Execute the job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->ad->hasPostId()) {
            $this->send();
        }
    }

    /**
     * Actually send comment
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return void
     */
    protected function send()
    {
        $response = $this->ad
            ->getFacebookClient()
            ->post($this->ad->getPostId() . '/comments', ['message' => $this->comment->message], $this->ad->pageToken())
            ->getDecodedBody();

        SentComment::create([
            'comment_id' => $response['id'],
            'profile_id' => 1,
            'sent_at'    => now(),
            'ad_id'      => $this->ad->id,
            'message'    => $this->comment->message,
        ]);
    }
}
