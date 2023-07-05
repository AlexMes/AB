<?php

namespace App\Deluge\Http\Controllers\Unity;

use App\Http\Controllers\Controller;
use App\UnityOrganization;

class RefreshOrganization extends Controller
{
    /**
     * @param UnityOrganization $organization
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(UnityOrganization $organization)
    {
        $this->authorize('update', $organization);

        $organization->clearIssue();
        $organization->refreshUnityData(true);

        return back()->with('message', 'Обновление данных организации запущено.');
    }
}
