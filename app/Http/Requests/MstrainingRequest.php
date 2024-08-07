<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MstrainingRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            return [
                "training_program" => "required|string|max:255",
            ];
        } else {
            return [
                "training_program" => "required|string|max:255",
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                "training_program.required"   => "Training program is required!",
                "training_program.max"        => "max input is 255 character!"
            ];
        } else {
            return [
                "training_program.required"   => "Training program is required!",
                "training_program.max"        => "max input is 255 character!"
            ];
        }
    }
}
