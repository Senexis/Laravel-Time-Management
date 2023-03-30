<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => 'required',
            'password'          => 'nullable|min:6',
            'email'             => 'required|email|unique:users,email,' . $this->route('user')->id,
            'timezone'          => 'required|timezone',
            'locale'            => 'required|in:' . implode(',', array_keys(config('locales'))),
            'role_id'           => 'exists:roles,id',
            'hourly_rate'       => 'required|numeric|between:0,9999.99',
            'travel_expenses'   => 'required|numeric|between:0,9999.99',
            'is_active'         => 'boolean',
            'timeular_id'       => 'nullable|numeric|min:0',
        ];
    }
}
