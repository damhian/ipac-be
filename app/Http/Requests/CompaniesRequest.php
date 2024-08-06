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
                'name' => 'required|string|max:50',
                'about' => 'required|string'
            ];
        } else {
            return [
                'name' => 'required|string|max:50',
                'about' => 'required|string'
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
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
