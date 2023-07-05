<?php

namespace App\Facebook\Traits;

trait HasBudgets
{

    /**
     * Format string to money format
     *
     * @param string $value
     *
     * @return string
     */
    public function getBudgetRemainingAttribute($value)
    {
        return number_format($value / 100, 2, '.', ',');
    }

    /**
     * Format string to money format
     *
     * @param string $value
     *
     * @return string
     */
    public function getDailyBudgetAttribute($value)
    {
        return number_format($value / 100, 2, '.', ',');
    }

    /**
     * Format string to money format
     *
     * @param string $value
     *
     * @return mixed
     */
    public function getLifetimeBudgetAttribute($value)
    {
        return number_format($value / 100, 2, '.', ',');
    }
}
