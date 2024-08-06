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
            'title' => 'max:50',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'story' => 'required|string'
        ];

        if ($this->isMethod('post')) {
            $rules = [
                'title' => 'max:50',
                'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
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
            'title.max' => 'maximal characters for title is 50!',
            'image.required' => 'image is required!',
            'image.mimes' => 'the images must be in these format: jpeg,png,jpg,svg',
            'image.max' => 'the maximum capacity of the image can upload is 2MB',
            'story.required' => 'story is required!'
        ];
    }
}
