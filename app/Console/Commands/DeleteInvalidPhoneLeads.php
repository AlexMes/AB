<?php

namespace App\Console\Commands;

use App\Lead;
use Illuminate\Console\Command;

class DeleteInvalidPhoneLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:delete-invalid-phone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes leads where invalid phone.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Lead::query()
            ->wherePhoneValid(false)
            ->whereOfferId(2006)
            ->get()
            ->each->delete();

        return 0;
    }
}
