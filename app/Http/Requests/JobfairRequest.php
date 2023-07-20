<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobfairRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            return [
                'title' => 'required|string|max:50',
                'content' => 'required|string',
                'short_description' => 'required|string|max:255',
                'region' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'jobtype' => 'required|in:Harian,Magang,Waktu Penuh,Paruh Waktu,Kontrak',
                'jobtitle' => 'required|string|max:255',
                'location_name' => 'required|string|max:50',
                'location_lon' => 'required|numeric',
                'location_lat' => 'required|numeric',
                'start_at' => 'required|date|date_format:Y-m-d',
                'end_at' => 'required|date|after_or_equal:start_at|date_format:Y-m-d',
            ];
        } else {
            return [
                'title'=> 'required|string|max:50',
                'content'=> 'required|string',
                'short_description'=> 'required|string|max:255',
                'region' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'jobtype' => 'required|in:Harian,Magang,Waktu Penuh,Paruh Waktu,Kontrak',
                'jobtitle' => 'required|string|max:255',
                'location_name'=> 'required|string|max:50',
                'location_lon'=> 'required|numeric',
                'location_lat'=> 'required|numeric',
                'start_at'=> 'required|date|date_format:Y-m-d',
                'end_at'=> 'required|date|after_or_equal:start_at|date_format:Y-m-d',
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                'title' => 'title is required!',
                'content' => 'content is required!',
                'short_description' => 'short description is required!',
                'region' => 'region is required!',
                'company' => 'company is required!',
                'jobtype' => 'jobtype is required!',
                'jobtype.in' => 'invalid input. please input one of this string option : Harian,Magang,Waktu Penuh,Paruh Waktu,Kontrak',
                'jobtitle' => 'jobtitle is required!',
                'location_name' => 'location name is required!',
                'location_lon.required' => 'location longitude is required!',
                'location_lon.numeric' => 'the format must be in number!',
                'location_lat.required' => 'location latitude is required!',
                'location_lat.numeric' => 'the format must be in number!',
                'start_at.required' => 'start datetime is required!',
                'start_at.date' => 'the start date must be a valid date with this format: Y-m-d!',
                'end_at.required' => 'end datetime is required!',
                'end_at.date' => 'the end date must be a valid date with this format: Y-m-d!',
                'end_at.after_or_equal' => 'the end date must be greater than or equal to the start date!'
            ];
        } else {
            return [
                'title' => 'title is required!',
                'content' => 'content is required!',
                'short_description' => 'short description is required!',
                'region' => 'region is required!',
                'company' => 'company is required!',
                'jobtype' => 'jobtype is required!',
                'jobtype.in' => 'invalid input. please input one of this string option : Harian,Magang,Waktu Penuh,Paruh Waktu,Kontrak',
                'jobtitle' => 'jobtitle is required!',
                'location_name' => 'location name is required!',
                'location_lon.required' => 'location longitude is required!',
                'location_lon.numeric' => 'the format must be in number!',
                'location_lat.required' => 'location latitude is required!',
                'location_lat.numeric' => 'the format must be in number!',
                'start_at.required' => 'start datetime is required!',
                'start_at.date' => 'the start date must be a valid date with this format: Y-m-d!',
                'end_at.required' => 'end datetime is required!',
                'end_at.date' => 'the end date must be a valid date with this format: Y-m-d!',
                'end_at.after_or_equal' => 'the end date must be greater than or equal to the start date!'
            ];
        }
    }
}
