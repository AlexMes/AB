<?php

namespace App\Leads\Pipes;

use App\BlackLead;
use App\Lead;
use Closure;
use Illuminate\Validation\ValidationException;

class FilterPhoneNumber implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        if ($this->shouldBlock($lead)) {
            throw ValidationException::withMessages([
                'general' => [
                    'Phone number is wrong. Submission rejected.'
                ]
            ]);
        }

        return $next($lead);
    }

    protected function shouldBlock(Lead $lead)
    {
        if ($lead->offer === null) {
            return false;
        }

        return BlackLead::whereBranchId($lead->offer->branch_id)
            ->wherePhone($lead->phone)
            ->exists();
    }
}
