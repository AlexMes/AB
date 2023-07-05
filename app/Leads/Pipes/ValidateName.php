<?php

namespace App\Leads\Pipes;

use App\Lead;
use App\Offer;
use App\Rules\ObsceneCensorRus;
use Closure;
use Illuminate\Support\Str;

class ValidateName implements LeadProcessingPipe
{
    public function handle(Lead $lead, Closure $next)
    {
        if (in_array(Offer::where('id', $lead->offer_id)->value('vertical'), ['mixed','Burj'])) {
            $lead->valid = $this->withoutTestInName($lead->fullname);

            return $next($lead);
        }

        $lead->valid = $this->isNotEmpty($lead->fullname)
            && $this->withoutAbusiveWords($lead->fullname)
            && $this->withoutTestInName($lead->fullname);

        return $next($lead);
    }

    protected function isNotEmpty(string $fullname): bool
    {
        return Str::length($fullname) > 1;
    }

    protected function withoutAbusiveWords(string $fullname): bool
    {
        return ObsceneCensorRus::isAllowed($fullname);
    }

    protected function withoutTestInName(string $fullname): bool
    {
        return ! Str::contains(Str::lower($fullname), ['test','ttes','demo','check','тест','проверка','демо','фуфел','госоподин']);
    }
}
