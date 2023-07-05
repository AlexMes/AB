<?php

namespace App\Jobs\SMS;

use App\AdsBoard;
use App\Branch;
use App\Notifications\SMS\LowBalance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class CheckBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Branch
     */
    protected Branch $branch;

    /**
     * @param Branch $branch
     */
    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->branch->isSmsServiceValid()) {
            $balance = $this->branch->initializeSmsService()->getBalance();
            if (isset($balance['money']) && isset($balance['currency']) && (float)$balance['money'] < 10) {
                Notification::send(
                    AdsBoard::devsChannel(),
                    new LowBalance($balance['money'], $balance['currency'], $this->branch->id)
                );
            }
        }
    }
}
