<?php

namespace App\Jobs;

use App\ManualApp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckManualAppStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var ManualApp
     */
    public ManualApp $app;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ManualApp $app)
    {
        $this->app = $app;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::get($this->app->link);

        if ($this->app->status === ManualApp::NEW && $response->successful()) {
            $this->app->update(['status' => ManualApp::PUBLISHED]);
        }

        if ($this->app->status === ManualApp::PUBLISHED && $response->status() === 404) {
            $this->app->update(['status' => ManualApp::BANNED]);
        }
    }
}
