<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StrukturorganisasiRequest extends FormRequest
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
            'nama'      => 'required|string|max:60',
            'jabatan'   => 'required|string|max:125',
            'level'     => 'required|numeric'
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }else{
            return [
                'nama'      => 'required|string|max:60',
                'jabatan'   => 'required|string|max:125',
                'level'     => 'required|numeric'
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nama.required'     => 'nama is required!',
            'nama.max'          => 'nama cannot exceed 60 characters!',
            'jabatan.required'  => 'jabatan is required!',
            'level.required'    => 'level is required!',
            'level.numeric'     => 'level must be a number!',
            'jabatan.max'       => 'jabatan cannot exceed 125 characters!',
            'image.required'    => 'image is required!',
            'image.mimes'       => 'the image must be a file of type: jpeg, png, jpg!',
            'image.max'         => 'the image size must not exceed 2MB!'
        ];
    }
}
