<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            "image" => "nullable|image|mimes:jpeg,png,jpg,svg|max:2048",
            "short_description" => "required|string|max:255",
            "location_name" => $this->input('type') === 'news' ? 'nullable|string|max:50' : 'required|string|max:50',
            "location_lon" => "nullable|numeric",
            "location_lat" => "nullable|numeric",
            "type" => "required|in:event,news",
            "start_at" => "nullable|date|date_format:Y-m-d", 
            "end_at" => "nullable|date|after_or_equal:start_at|date_format:Y-m-d",
            "event_time" => ["nullable"]
        ];

        if ($this->input('type') !== 'news') {
            $rules['start_at'] .= '|required';
            $rules['end_at'] .= '|required';
        }

        return $rules;
        
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                "title" => "title is required!",
                "content" => "content is required!",
                "image.mimes" => "the images must be in these format: jpeg,png,jpg,svg",
                "image.max" => "the maximum capacity of the image can upload is 2MB",
                "short_description" => "short description is required!",
                "location_name" => "location name is required!",
                "location_lon.numeric" => "the format must be in number!",
                "location_lat.numeric" => "the format must be in number!",
                "start_at" => "start datetime is required!",
                "start_at.date" => "the start date must be a valid date with this format: Y-m-d!",
                "end_at.date" => "the end date must be a valid date with this format: Y-m-d!",
                "end_at.after_or_equal" => "the end date must be greater than or equal to the start date!",
            ];
        } else {
            return [
                "title" => "title is required!",
                "content" => "content is required!",
                "image" => "image is required!",
                'image.mimes' => 'the images must be in these format: jpeg,png,jpg,svg',
                'image.max' => 'the maximum capacity of the image can upload is 2MB',
                "short_description" => "short description is required!",
                "location_name" => "location name is required!",
                "location_lon.numeric" => "the format must be in number!",
                "location_lat.numeric" => "the format must be in number!",
                "start_at.date" => "the start date must be a valid date with this format: Y-m-d!",
                "end_at.date" => "the end date must be a valid date with this format: Y-m-d!",
                "end_at.after_or_equal" => "the end date must be greater than or equal to the start date!",
            ];
        }
    }
}
