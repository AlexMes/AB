<?php

namespace App\Http\Controllers;

use App\LeadOrderRoute;

class IndexRouteAssignments extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\LeadOrderRoute $route
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(LeadOrderRoute $route)
    {
        return $route->assignments()->visible()
            ->with([
                'lead' => function ($query) {
                    $query->select(['id', 'phone', 'firstname', 'lastname', 'middlename', 'ip', 'phone_valid', 'email', 'user_id']);
                },
                'lead.ipAddress' => fn ($ipQuery) => $ipQuery->select('ip', 'country_name'),
                'lead.user'      => fn ($userQuery) => $userQuery->select('id', 'name'),
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->append(['autologin', 'actual_benefit']);
    }
}
