<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserstoryRequest extends FormRequest
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
            'story' => 'required|string'
        ];

        if ($this->isMethod('post')) {
            $rules = [
                'story' => 'required|string'
            ];
        } else {
            return [
                'message' => 'The method other than POST is not supported!'
            ];
        }

        return $rules;

    }

    public function messages():array
    {
        return [
            'story.required' => 'story is required!'
        ];
    }
}
