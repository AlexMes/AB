<?php

namespace App\Http\Requests;

use App\Rules\TransferManagerShouldHaveSameOffice;
use Illuminate\Foundation\Http\FormRequest;

class TransferLeadOrderAssignment extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('transfer', $this->route('assignment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $order = $this->route('assignment')->route->order;

        return [
            'manager_id' => [
                'exists:managers,id',
                new TransferManagerShouldHaveSameOffice($order),
            ],
        ];
    }
}
