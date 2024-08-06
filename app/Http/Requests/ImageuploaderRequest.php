<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageuploaderRequest extends FormRequest
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
        return [
            "image" => "image|mimes:jpeg,png,jpg|max:2048",
            "image_url" => "string",
        ];
    }

    public function messages()
    {
        return [
            "image.mimes" => "the image must be a file of type: jpeg, png, jpg!",
            "image.max" => "the image size must not exceed 2MB!",
        ];
    }
}
