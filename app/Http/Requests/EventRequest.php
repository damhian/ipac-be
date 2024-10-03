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

    protected function prepareForValidation()
    {
        $this->merge([
            'start_at' => $this->input('start_at') === 'undefined' ? null : $this->input('start_at'),
            'end_at' => $this->input('end_at') === 'undefined' ? null : $this->input('end_at'),
        ]);

        if ($this->input('type') === 'news' && !$this->filled('location_name')) {
            $this->merge(['location_name' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string|max:50",
            "content" => "required|string",
            "image" => "nullable|image|mimes:jpeg,png,jpg,svg|max:2048",
            "short_description" => "required|string|max:255",
            "location_name" => $this->input('type') === 'news' ? 'nullable|string|max:50' : 'required|string|max:50',
            "location_lon" => "nullable|numeric",
            "location_lat" => "nullable|numeric",
            "type" => "required|in:event,news",
            "start_at" => $this->input('type') === 'event' ? 'required|date' : 'nullable|date',
            "end_at" => $this->input('type') === 'event' ? 'required|date|after_or_equal:start_at' : 'nullable|date|after_or_equal:start_at',
        ];        
    }

    public function messages()
    {
        return [
            "title" => "title is required!",
            "content" => "content is required!",
            "image.mimes" => "the images must be in these format: jpeg,png,jpg,svg",
            "image.max" => "the maximum capacity of the image can upload is 2MB",
            "short_description" => "short description is required!",
            "location_name" => "location name is required!",
            "location_lon.numeric" => "the format must be in number!",
            "location_lat.numeric" => "the format must be in number!",
            "start_at.required" => "The start date is required for events!",
            "end_at.required" => "The end date is required for events!",
            "end_at.after_or_equal" => "The end date must be on or after the start date!",
        ];
    }
}
