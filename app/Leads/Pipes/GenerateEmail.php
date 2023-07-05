<?php

namespace App\Leads\Pipes;

use App\Lead;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GenerateEmail implements LeadProcessingPipe
{
    /**
     * @throws \Exception
     */
    public function handle(Lead $lead, Closure $next)
    {
        if ($lead->email !== null || !optional($lead->offer)->generate_email) {
            return $next($lead);
        }

        $lead->email = Str::lower($this->generateSlugFromName($lead) . $this->generateEmail($lead));

        Cache::put(sprintf('ge-%s', $lead->email), true, now()->addMinute());

        return $next($lead);
    }

    /**
     * @param Lead $lead
     *
     * @return string
     */
    public function generateSlugFromName(Lead $lead): string
    {
        $firstNamePart  = mb_substr($lead->firstname, 0, (int) round(rand(1, strlen($lead->firstname) / 2)));
        $lastNamePart   = mb_substr($lead->lastname, 0, (int) round(rand(1, strlen($lead->lastname) / 2)));
        $middleName     = $lead->middlename ?? $lead->firstname;
        $middleNamePart = mb_substr($middleName, 0, (int) round(rand(1, strlen($middleName) / 2)));

        $nameArray = [$firstNamePart, $lastNamePart, $middleNamePart];
        shuffle($nameArray);

        return Str::slug(implode('', $nameArray));
    }

    public function generateEmail($lead): string
    {
        if (optional($lead->ipAddress)->country_code == 'RU') {
            return Arr::random(['@gmail.com', '@mail.ru']);
        }

        return  Arr::random(['@gmail.com', '@yahoo.com']);
    }
}
