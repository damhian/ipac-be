<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserexperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'company_id'    => 'required',
            'position'      => 'required|string|max:20',
            'start_at'      => 'required|date|date_format:Y-m-d',
            'end_at'        => 'required|date|after:start_at|date_format:Y-m-d'
        ];

        return $rules;
    }

    public function messages():array
    {
        return [
            'company_id'        => 'company is required!',
            'position.required' => 'position is required!',
            'position.max'      => 'max input is 20 character',
            'start_at.required' => 'start datetime is required!',
            'start_at.date'     => 'the start date must be a valid date with this format: Y-m-d!',
            'end_at.required'   => 'end datetime is required!',
            'end_at.date'       => 'the start date must be a valid date with this format: Y-m-d!',
            'end_at.after'      => 'the end date must be greater than start date!'
        ];
    }
}
