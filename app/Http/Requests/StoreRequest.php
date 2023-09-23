<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
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
            "title" => "required|string|max:50",
            "content" => "required|string",
            "short_description" => "required|string|max:255",
            "price" => "required|numeric",
            "link" => "nullable"
        ];

            if ($this->isMethod('post')) {
                $rules['images'] = 'required|array';
                $rules['images.*'] = 'image|mimes:jpeg,png,jpg|max:2048';

            } else {
                return [
                    "title"=> "required|string|max:50",
                    "content"=> "required|string",
                    "short_description"=> "required|string|max:255",
                    "price"=> "required|numeric",
                ];
            }
            
        return $rules;
        
    }

    public function messages():array
    {
        return [
            'title.required' => 'title is required!',
            'content.required' => 'content is required!',
            'short_description.required' => 'short description is required!',
            'price.required' => 'price is required!',
            'price.numeric' => 'the price must be a number!',
            'images.required' => 'at least one image is required!',
            'images.array' => 'the images must be an array!',
            'images.*.image' => 'the file must be an image!',
            'images.*.mimes' => 'the image must be a file of type: jpeg, png, jpg!',
            'images.*.max' => 'the image size must not exceed 2MB!',
        ];
    }
}
