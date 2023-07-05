<?php

namespace App\Console\Commands;

use App\Lead;
use Illuminate\Console\Command;

class UpdateRussianPhoneNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:update-russian-phone-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::where('phone', 'like', '8%')->cursor();
        $leads->each(fn ($lead) => $lead->update([
            'phone' => $lead->formatted_phone,
        ]));

        return 0;
    }
}
