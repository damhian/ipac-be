<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'license_number' => 'nullable|integer|digits_between:1,18',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'image' => 'mimes:jpeg,png,jpg,svg|max:5120',
            'tahun_masuk' => 'required|integer',
            'tahun_lulus' => 'nullable|integer',
            'training_program' => 'nullable|string|max:80',
            'batch' => 'nullable|string|max:25',
            'current_job' => 'nullable|string|max:255',
            'current_workplace' => 'nullable|string|max:255',
            'birth_place' => 'nullable|string|max:80',
            'nationality' => 'required|string|max:80',
            'date_of_birth' => 'nullable|date|date_format:Y-m-d',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'phone_number_code' => 'nullable|string|max:4',
            'gender' => 'required|string|max:12',
            'current_status' => 'required|string|max:60'
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'license_number.integer' => 'The license number must be an integer!',
            'license_number.digits_between' => 'maximum number input for license_number cannot exceed 12 digit!',
            'first_name.required' => 'The first name field is required!',
            'first_name.max' => 'The first name may not be greater than :max characters.',
            'last_name.max' => 'The last name may not be greater than :max characters.',
            'image.mimes' => 'the images must be in these format: jpeg,png,jpg,svg',
            'image.max' => 'the maximum capacity of the image can upload is 5MB',
            'tahun_masuk.required' => 'The tahun masuk field is required!',
            'tahun_masuk.integer' => 'The tahun masuk must be an integer!',
            'tahun_lulus.integer' => 'The tahun lulus must be an integer!',
            'training_program.max' => 'The training program may not be greater than :max characters!',
            'batch.max' => 'The batch may not be greater than :max characters.',
            'current_workplace.max' => 'The current workplace may not be greater than :max characters.',
            'birth_place.max' => 'The birth place may not be greater than :max characters.',
            'nationality.required' => 'nationality is required!',
            'nationality.max' => 'maximum input for nationality is cannot exceed 191 charaters!',
            'date_of_birth.date' => 'The date of birth must be a valid date with this format: Y-m-d!',
            'address.max' => 'The address may not be greater than :max characters.',
            'phone_number.required' => 'The phone number field is required!',
            'phone_number.max' => 'The phone number may not be greater than :max characters.',
            'phone_number_code.required' => 'The phone number code field is required!',
            'phone_number_code.max' => 'The phone number code may not be greater than :max characters.',
            'gender.required' => 'The gender field is required!',
            'gender.max' => 'The gender may not be greater than :max characters.',
            'current_status.required' => 'Current status is required!',
            'current_status.max' => 'Current status may not be greater than :max characters.'
        ];
    }
}
