<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomRequestFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'description' => 'required|string|max:1000|regex:/^[a-zA-Z0-9\s\-_.,()!?]+$/',
            'materials' => 'nullable|string|max:500|regex:/^[a-zA-Z0-9\s\-_.,()]+$/',
            'dimensions' => 'nullable|string|max:100|regex:/^[a-zA-Z0-9\s\-_.,()x×]+$/',
            'style_preferences' => 'nullable|string|max:500|regex:/^[a-zA-Z0-9\s\-_.,()!?]+$/',
            'budget' => 'nullable|numeric|min:0|max:999999.99',
            'deadline' => 'nullable|date|after:today|before:' . now()->addYear(),
            'artist_expertise' => 'nullable|string|max:500|regex:/^[a-zA-Z0-9\s\-_.,()]+$/',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'description.required' => 'Description is required for your custom request.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'description.regex' => 'Description contains invalid characters.',
            'materials.max' => 'Materials description cannot exceed 500 characters.',
            'materials.regex' => 'Materials description contains invalid characters.',
            'dimensions.max' => 'Dimensions cannot exceed 100 characters.',
            'dimensions.regex' => 'Dimensions contain invalid characters.',
            'style_preferences.max' => 'Style preferences cannot exceed 500 characters.',
            'style_preferences.regex' => 'Style preferences contain invalid characters.',
            'budget.numeric' => 'Budget must be a valid number.',
            'budget.min' => 'Budget cannot be negative.',
            'budget.max' => 'Budget cannot exceed 999,999.99.',
            'deadline.date' => 'Deadline must be a valid date.',
            'deadline.after' => 'Deadline must be in the future.',
            'deadline.before' => 'Deadline cannot be more than one year from now.',
            'artist_expertise.max' => 'Artist expertise cannot exceed 500 characters.',
            'artist_expertise.regex' => 'Artist expertise contains invalid characters.',
            'images.*.image' => 'All files must be valid images.',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, or GIF format.',
            'images.*.max' => 'Each image must not exceed 2MB in size.',
            'images.*.dimensions' => 'Images must be between 100x100 and 4000x4000 pixels.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'description' => 'description',
            'materials' => 'materials',
            'dimensions' => 'dimensions',
            'style_preferences' => 'style preferences',
            'budget' => 'budget',
            'deadline' => 'deadline',
            'artist_expertise' => 'artist expertise',
            'images.*' => 'images',
        ];
    }
}
