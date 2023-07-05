<?php

namespace App\Http\Requests;

use App\LeadOrderAssignment;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignmentDeliveryFailResend extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->id === 230) {
            return false;
        }

        if ($this->user()->hasRole([User::ADMIN, User::SUPPORT])) {
            return true;
        }

        if ($this->user()->isBranchHead()) {
            /** @var LeadOrderAssignment $assignment */
            $assignment = $this->route('assignment');

            return $this->user()->allowedOffers->contains($assignment->lead->offer)
                && (
                    $assignment->lead->user_id === null || $this->user()->branch->users()
                        ->withTrashed()
                        ->where('users.id', $assignment->lead->user_id)
                        ->exists()
                );
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
