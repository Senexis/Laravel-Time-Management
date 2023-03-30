<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUsersRequest extends FormRequest
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
            'password'          => 'required|min:6',
            'email'             => 'required|email|unique:users,email',
            'timezone'          => 'required|timezone',
            'locale'            => 'required|in:' . implode(',', array_keys(config('locales'))),
            'role_id'           => 'required|exists:roles,id',
            'hourly_rate'       => 'required|numeric|between:0,9999.99',
            'travel_expenses'   => 'required|numeric|between:0,9999.99',
            'is_active'         => 'boolean',
            'timeular_id'       => 'nullable|numeric|min:0',
        ];
    }
}
