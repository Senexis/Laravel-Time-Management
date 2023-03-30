<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTimeEntriesRequest extends FormRequest
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
            'project_id'   => 'required|exists:projects,id',
            'work_type_id' => 'required|exists:work_types,id',
            'location_id'  => 'required|exists:user_locations,id',
            'start_time'   => 'sometimes|date',
            'end_time'     => 'sometimes|date|after:start_time',
        ];
    }
}
