<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteLeadOrderRoute;
use App\Jobs\TransferLeadOrderRoute;
use App\LeadOrderRoute;
use App\Manager;

class DestroyOrderRoute extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LeadOrderRoute $route, DeleteLeadOrderRoute $request)
    {
        switch ($request->action) {
            case 'leftovers':
                $route->assignments->each->remove();
                $route->delete();
                break;
            case 'delete_leads':
                $leads = $route->leads;
                $route->assignments->each->remove();
                $leads->each->delete();
                $route->delete();
                break;
            case 'transfer':
                TransferLeadOrderRoute::withChain([
                    fn () => $route->delete(),
                ])->dispatch($route, Manager::find($request->manager_id));
                break;
            case 'distribute':
                $route->distribute();
                $route->delete();
                break;
            default:
                $route->delete();
        }

        return response()->noContent();
    }
}
