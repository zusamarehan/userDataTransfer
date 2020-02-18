<?php

namespace App\Http\Requests;

use App\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;

class CollaboratorTransfer extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_user_id' => ['required', new UserExists(), 'exists:project_user,user_id'],
            'to_user_id' => ['required', 'exists:users,id']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'from_user_id.required' => 'The Collaborator ID to Transfer the data from is required',
            'from_user_id.exists'  => 'Collaborator ID has no assigned Projects',

            'to_user_id.required' => 'The Collaborator ID to Transfer the data to is required',
            'to_user_id.exists'  => 'The Collaborator ID does not exists!',
        ];
    }
}
