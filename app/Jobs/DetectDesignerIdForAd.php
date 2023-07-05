<?php

namespace App\Jobs;

use App\Designer;
use App\Facebook\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectDesignerIdForAd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Ad
     */
    protected Ad $ad;

    /**
     * Create a new job instance.
     *
     * @param Ad $ad
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $designer = null;
        if (preg_match("~[0-9]{4}_([^_]+)_~", $this->ad->name, $match) && !empty($match[1])) {
            $designer = Designer::whereRaw('lower(name)=?', strtolower($match[1]))->first();
        }

        if ($this->ad->designer_id !== optional($designer)->id) {
            $this->ad->designer_id = optional($designer)->id;
            $this->ad->saveQuietly();
        }
    }
}
