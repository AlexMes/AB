<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Trail
{
    protected $entries = [];

    public function add(string $message)
    {
        $this->entries[] = ['ts' => microtime(true), 'msg' => $message];
    }

    public function send()
    {
        if (count($this->entries) > 0) {
            AdsBoard::report(collect($this->entries)
                ->map(
                    fn ($entry) => sprintf("*%s*: %s", $entry['ts'], Str::limit($entry['msg'], 250))
                )->implode(PHP_EOL) . $this->turnover());
        }
    }

    /**
     * Get total time taken by request
     *
     * @return string
     */
    protected function turnover(): string
    {
        return sprintf(
            "%s%sRequest turnover is *%s ms*",
            PHP_EOL,
            PHP_EOL,
            Carbon::parse(LARAVEL_START)->diffInMilliseconds(now())
        );
    }
}
