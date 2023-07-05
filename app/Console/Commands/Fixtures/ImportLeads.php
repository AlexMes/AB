<?php

namespace App\Console\Commands\Fixtures;

use App\Jobs\ProcessLead;
use Illuminate\Console\Command;
use Storage;

class ImportLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:imports-leads';

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
        $handler = fopen(storage_path('leads.csv'), 'r');

        while (($data = fgetcsv($handler, 1000, ";")) !== false) {
            $lead = ProcessLead::dispatch([
                'domain'       => 'orlenpl-quiz.info',
                'firstname'    => $data[0],
                'lastname'     => $data[1],
                'phone'        => $data[2],
                'email'        => $data[3],
                'utm_source'   => $data[4],
                'utm_medium'   => $data[5],
                'utm_campaign' => $data[6],
                'utm_content'  => $data[7],
                'utm_term'     => $data[8]
            ]);

            $this->info('Stored lead ' . optional($lead)->id ?? '. Not stored.' . $data[1]);
        }

        fclose($handler);

        return 0;
    }
}
