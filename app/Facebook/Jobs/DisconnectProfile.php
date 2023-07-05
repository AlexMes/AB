<?php

namespace App\Facebook\Jobs;

use App\Facebook\FacebookApp;
use App\Facebook\Profile;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DisconnectProfile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var FacebookApp
     */
    protected FacebookApp $app;

    /**
     * @var string
     */
    protected string $signed;

    /**
     * Create a new job instance.
     *
     * @param string $signed
     */
    public function __construct($signed)
    {
        $this->app    = app('facebook')::current();
        $this->signed = $signed;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $parsed = $this->parseSigned();

        $profile = Profile::query()
            ->where('fbId', $parsed['user_id'])
            ->whereAppId($this->app->id)
            ->first();

        if (optional(User::find($profile->user_id))->can('destroy', $profile)) {
            $profile->remove();
        }
    }

    /**
     * @throws \Throwable
     *
     * @return mixed
     */
    protected function parseSigned()
    {
        list($encoded_sig, $payload) = explode('.', $this->signed, 2);

        // decode the data
        $sig  = $this->urlDecodeBase64($encoded_sig);
        $data = json_decode($this->urlDecodeBase64($payload), true);

        // confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $this->app->secret, $raw = true);
        throw_if($sig !== $expected_sig, new \Exception('Bad Signed JSON signature!'));

        return $data;
    }

    /**
     * @param $input
     *
     * @return false|string
     */
    protected function urlDecodeBase64($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
