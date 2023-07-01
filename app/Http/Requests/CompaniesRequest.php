<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompaniesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        
        // if (Auth::user()->role == "admin") {
        //     return true;
        // } else {
        //     return false;
        // }
        
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
                'image_url' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
                'name' => 'required|string|max:50',
                'about' => 'required|string'
            ];
        } else {
            return [
                'image_url' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
                'name' => 'required|string|max:50',
                'about' => 'required|string'
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                'image_url.required' => 'image is required!',
                'image.mimes' => 'the images must be in these format: jpeg,png,jpg,svg',
                'image.max' => 'the maximum capacity of the image can upload is 2MB',
                'name.required' => 'name is required!',
                'about.required' => 'about is required!'
            ];
        } else {
            return [
                'name.required' => 'name is required!',
                'about.required' => 'about is required!'
            ];
        }
    }
}
