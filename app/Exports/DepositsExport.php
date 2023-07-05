<?php

namespace App\Exports;

use App\Deposit;
use App\Http\Controllers\DepositsController;
use App\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class DepositsExport implements FromArray, WithStrictNullComparison
{
    protected $deposits;

    public function __construct($filters)
    {
        $this->deposits = (new DepositsController())->index($filters->merge(['all' => true]));
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $deposits = $this->deposits
            ->reject(function (Deposit $item) {
                return empty($item->office_id);
            })
            ->map(function (Deposit $item, $key) {
                $result = [
                    'id'                => $item->id,
                    'lead_created'      => $item->lead ? $item->lead->created_at->toDateTimeString() : '',
                    'lead_return_date'  => $item->lead_return_date,
                    'date'              => $item->date,
                    'click_id'          => $item->lead ? $item->lead->clickid : '',
                    'account'           => $item->account ? $item->account->name : '',
                    'user'              => $item->user ? $item->user->name : '',
                    'office'            => $item->office ? $item->office->name : '',
                    'offer'             => $item->offer ? $item->offer->name : '',
                    'phone'             => $item->phone,
                    'sum'               => $item->sum,
                    'updated'           => optional($item->updated_at)->toDateTimeString(),
                    'utm_source'        => $item->lead ? $item->lead->utm_source : '',
                    'utm_content'       => $item->lead ? $item->lead->utm_content : '',
                    'utm_campaign'      => $item->lead ? $item->lead->utm_campaign : '',
                    'utm_term'          => $item->lead ? $item->lead->utm_term : '',
                    'utm_medium'        => $item->lead ? $item->lead->utm_medium : '',
                    'app_id'            => $item->lead ? $item->lead->app_id : '',
                ];

                if (!auth()->user()->hasRole([User::ADMIN, User::SUPPORT, User::HEAD, User::TEAMLEAD])) {
                    unset($result['user']);
                    unset($result['office']);
                    unset($result['phone']);
                    unset($result['sum']);
                }
                if (auth()->user()->hasRole([User::HEAD]) && auth()->user()->branch_id === 19) {
                    $result['phone'] = '***********';
                }

                return $result;
            });

        return array_merge([array_keys($deposits->first())], $deposits->toArray());
    }
}
