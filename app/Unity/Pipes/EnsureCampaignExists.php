<?php

namespace App\Unity\Pipes;

use App\UnityCampaign;
use Closure;

class EnsureCampaignExists
{
    /**
     * @param array    $attributes
     * @param \Closure $next
     *
     * @return void
     */
    public function handle(array $attributes, Closure $next)
    {
        if (UnityCampaign::whereId($attributes['campaign_id'])->exists()) {
            return $next($attributes);
        }
    }
}
