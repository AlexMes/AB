<?php

namespace App\DestinationDrivers;

class CallResult
{
    protected $id;
    protected $status;
    protected $isDeposit;
    protected $depositDate;
    protected $depositSum;

    /**
     * Array shape as follows
     * id - external ID
     * status - obvi
     * isDeposit - true/false
     * depositDate - date Y-m-d
     * depositSum - sum of the deposit
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes)
    {
        $this->id          = $attributes['id'];
        $this->status      = $attributes['status'];
        $this->isDeposit   = $attributes['isDeposit'] ?? false;
        $this->depositDate = $attributes['depositDate'] ?? null;
        $this->depositSum  = $attributes['depositSum'] ?? null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isDeposit()
    {
        return $this->isDeposit ?? false;
    }

    public function getDepositDate()
    {
        return $this->depositDate ?? now()->toDateString();
    }

    public function getDepositSum()
    {
        return $this->depositSum ?? 151;
    }
}
