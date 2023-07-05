<?php

namespace App\Rules;

use App\Manager;
use Illuminate\Contracts\Validation\Rule;

class TransferManagerShouldHaveSameOffice implements Rule
{
    protected $order;

    /**
     * Create a new rule instance.
     *
     * @param mixed $order
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->order->office_id == optional(Manager::find($value))->office_id;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Manager should be from the same office.';
    }
}
