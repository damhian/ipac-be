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
            'license_number' => 'required|integer',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            "image" => "image|mimes:mimes:jpeg,png,jpg,svg|max:5120",
            'tahun_masuk' => 'required|integer',
            'tahun_lulus' => 'required|integer',
            'training_program' => 'required|string|max:80',
            'batch' => 'required|string|max:25',
            'current_job' => 'required|in:121,135,91,141,TNI,Polri,PNS,Pensiunan,Belum bekerja',
            'current_workplace' => 'required|string|max:255',
            'birth_place' => 'required|string|max:80',
            'date_of_birth' => 'required|date|date_format:Y-m-d',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:15',
            'phone_number_code' => 'required|string|max:4',
            'gender' => 'required|string|max:12',
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
            'license_number.required' => 'The license number field is required.',
            'license_number.integer' => 'The license number must be an integer.',
            'first_name.required' => 'The first name field is required.',
            'first_name.max' => 'The first name may not be greater than :max characters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.max' => 'The last name may not be greater than :max characters.',
            'image.mimes' => 'the images must be in these format: jpeg,png,jpg,svg',
            'image.max' => 'the maximum capacity of the image can upload is 5MB',
            'tahun_masuk.required' => 'The tahun masuk field is required.',
            'tahun_masuk.integer' => 'The tahun masuk must be an integer.',
            'tahun_lulus.required' => 'The tahun lulus field is required.',
            'tahun_lulus.integer' => 'The tahun lulus must be an integer.',
            'training_program.required' => 'The training program field is required.',
            'training_program.max' => 'The training program may not be greater than :max characters.',
            'batch.required' => 'The batch field is required.',
            'batch.max' => 'The batch may not be greater than :max characters.',
            'current_job.required' => 'The current job field is required.',
            'current_job.in' => 'The selected current job is invalid.',
            'current_workplace.required' => 'The current workplace field is required.',
            'current_workplace.max' => 'The current workplace may not be greater than :max characters.',
            'birth_place.required' => 'The birth place field is required.',
            'birth_place.max' => 'The birth place may not be greater than :max characters.',
            'date_of_birth.required' => 'The date of birth field is required.',
            'date_of_birth.date' => 'The date of birth must be a valid date with this format: Y-m-d!',
            'address.max' => 'The address may not be greater than :max characters.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.max' => 'The phone number may not be greater than :max characters.',
            'phone_number_code.required' => 'The phone number code field is required.',
            'phone_number_code.max' => 'The phone number code may not be greater than :max characters.',
            'gender.required' => 'The gender field is required.',
            'gender.max' => 'The gender may not be greater than :max characters.',
        ];
    }
}
