<?php

namespace App\Http\Requests\Offices;

use App\Manager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DeleteOfficeManager extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('destroy', $this->route('manager'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var Manager $manager */
        $manager = $this->route('manager');

        throw_if(
            $this->input('assign_type') == 'excludeManagers'
                && $manager->office->managers
                    ->pluck('id')
                    ->diff($this->input('managers', []))
                    ->count() <= 1,
            ValidationException::withMessages(['managers' => 'You cannot exclude all managers.'])
        );

        return [
            'managers'   => ['required_if:assign_type,onlyManagers', 'array'],
            'managers.*' => [
                Rule::exists('managers', 'id')->where('office_id', $manager->office_id),
                Rule::notIn($manager->id)
            ],
            'assign_type' => ['required', Rule::in(['onlyManagers', 'excludeManagers'])],
        ];
    }
}
