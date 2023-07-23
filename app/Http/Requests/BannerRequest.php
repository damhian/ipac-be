<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BannerRequest extends FormRequest
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
                "title" => "required|string|max:50",
                "content" => "required|string",
                "short_description" => "required|string|max:255",
                "file" => "required|file|mimetypes:image/jpeg,image/png,image/jpg,image/svg,video/mp4,video/quicktime|max:20480" // 20MB limit for file
            ];
        } else {
            return [
                "title" => "required|string|max:50",
                "content" => "required|string",
                "short_description" => "required|string|max:255",
                "file" => "required|file|mimetypes:image/jpeg,image/png,image/jpg,image/svg,video/mp4,video/quicktime|max:20480" // 20MB limit for file
            ];
        }
    }

    public function messages()
    {
         return [
            "title" => "title is required!",
            "content" => "content is required!",
            "short_description" => "short description is required!",
            "file" => "file is required!",
            "file.mimetypes" => "the file must be in these format: jpeg, png, jpg, svg, mp4, quicktime",
            "file.max" => "the maximum capacity of the file can upload is 20MB"
        ];
    }
}
