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
        if (Auth::user()->role == "admin") {
            return true;
        } else {
            return false;
        }

        // return true;
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
                "image" => "required|image|mimes:jpeg,png,jpg,svg|max:2048"
            ];
        } else {
            return [
                "title" => "required|string|max:50",
                "content" => "required|string",
                "short_description" => "required|string|max:255",
                "image" => "required|image|mimes:jpeg,png,jpg,svg|max:2048"
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                "title" => "title is required!",
                "content" => "content is required!",
                "short_description" => "short description is required!",
                "image" => "image is required!",
                "image.mimes" => "the images must be in these format: jpeg,png,jpg,svg",
                "image.max" => "the maximum capacity of the image can upload is 2MB"
            ];
        } else {
            return [
                "title" => "title is required!",
                "content" => "content is required!",
                "short_description" => "short description is required!",
            ];
        }
    }
}
