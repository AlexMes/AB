<?php

namespace App\Console\Commands;

use App\CRM\Events\Callback;
use Illuminate\Console\Command;

class NotifyManagerAboutCallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:manager-callbacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies managers about callbacks.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = \App\CRM\Callback::scheduled()
            ->incomplete()
            ->where('call_at', now()->addMinutes(5)->toDateTimeString('minute'));

        /** @var \App\CRM\Callback $callback */
        foreach ($query->cursor() as $callback) {
            Callback::dispatch($callback);
        }

        return 0;
    }
}
