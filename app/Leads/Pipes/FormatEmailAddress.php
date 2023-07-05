<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;
use Str;

class FormatEmailAddress implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        if ($lead->email === null) {
            return $next($lead);
        }

        $email = filter_var($lead->email, FILTER_SANITIZE_EMAIL);
        if (Str::endsWith($email, '.')) {
            $email = substr($email, 0, -1);
        }
        if (Str::contains($email, '.@')) {
            $email = str_replace('.@', '@', $email);
        }

        $lead->email = $email;

        return $next($lead);
    }
}
