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
                "tipe" => "required|in:home,profile,store,about", 
                "file" => "required|file|mimetypes:image/jpeg,image/png,image/jpg,image/svg,video/mp4,video/quicktime|max:20480" // 20MB limit for file
            ];
        } else {
            return [
                "tipe" => "required|in:home,profile,store,about",
                "file" => "required|file|mimetypes:image/jpeg,image/png,image/jpg,image/svg,video/mp4,video/quicktime|max:20480" // 20MB limit for file
            ];
        }
    }

    public function messages()
    {
         return [
            "tipe" => "type is required!",
            "tipe.in" => "invalid input. please input one of this string option : home,profile,store,about",
            "file" => "file is required!",
            "file.mimetypes" => "the file must be in these format: jpeg, png, jpg, svg, mp4, quicktime",
            "file.max" => "the maximum capacity of the file can upload is 20MB"
        ];
    }
}
