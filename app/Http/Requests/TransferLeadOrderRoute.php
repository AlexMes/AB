<?php

namespace App\Http\Requests;

use App\Rules\TransferManagerShouldHaveSameOffice;
use Illuminate\Foundation\Http\FormRequest;

class TransferLeadOrderRoute extends FormRequest
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
        $order = $this->route('route')->order;

        return [
            'manager_id' => [
                'exists:managers,id',
                //                new TransferManagerShouldHaveSameOffice($order),
            ],
        ];
    }
}
