<?php

namespace App\Http\Requests;

use App\LeadOrderRoute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ChangeLeadOrderRouteOffer extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() && $this->user()->id !== 230
                || $this->user()->isSupport()
                || $this->user()->isDeveloper()
                || $this->user()->isSubSupport()
                || $this->user()->isBranchHead() && $this->user()->can('update', $this->route('route'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var LeadOrderRoute $route */
        $route = $this->route('route');

        throw_if($route->isCompleted(), ValidationException::withMessages(['route' => 'Route should be incompleted.']));

        return [
            'offer_id'  => [
                'required',
                'exists:offers,id',
            ],
        ];
    }
}
