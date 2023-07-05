<?php

namespace App\Jobs\Imports;

use App\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportDeposit implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Deposits instance
     *
     * @var \App\Deposit
     */
    protected $deposits;

    /**
     * Constructor.
     *
     * @param $deposit
     * @param mixed $deposits
     */
    public function __construct($deposits)
    {
        $this->deposits = $deposits;
    }

    /**
     * Process a job
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->deposits as $deposit) {
            Deposit::create($deposit);
        };
    }
}
