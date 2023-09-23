<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MsvisimisiRequest extends FormRequest
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
                "type" => "required|in:visi,misi,about",
                "content" => "required|string",
            ];
        } else {
            return [
                "type" => "required|in:visi,misi,about",
                "content" => "required|string",
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                "type.required"     => "type is required!",
                "type.in"           => "invalid input. please input one of this string option : visi, misi, about",
                "content.required"  => "content is required!"
            ];
        } else {
            return [
                "type.required"     => "type is required!",
                "type.in"           => "invalid input. please input one of this string option : visi, misi, about",
                "content.required"  => "content is required!"
            ];
        }
    }
}
