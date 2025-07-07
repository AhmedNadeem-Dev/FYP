<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-_.,()!]+$/',
            'description' => 'required|string|max:2000|regex:/^[a-zA-Z0-9\s\-_.,()!@#$%^&*+=<>\/\\|`~\n\r]+$/',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'category_id' => 'required|integer|exists:categories,category_id',
            'additional_info' => 'nullable|string|max:1000|regex:/^[a-zA-Z0-9\s\-_.,()!@#$%^&*+=<>\/\\|`~\n\r]+$/',
            'images' => 'required|array|min:1|max:5',
            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048', // 2MB max per image
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.regex' => 'Product name contains invalid characters.',
            'name.max' => 'Product name must not exceed 255 characters.',
            
            'description.required' => 'Product description is required.',
            'description.regex' => 'Product description contains invalid characters.',
            'description.max' => 'Product description must not exceed 2000 characters.',
            
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Product price must be a valid number.',
            'price.min' => 'Product price must be at least $0.01.',
            'price.max' => 'Product price cannot exceed $999,999.99.',
            
            'category_id.required' => 'Product category is required.',
            'category_id.exists' => 'Selected category is invalid.',
            
            'additional_info.regex' => 'Additional information contains invalid characters.',
            'additional_info.max' => 'Additional information must not exceed 1000 characters.',
            
            'images.required' => 'At least one product image is required.',
            'images.min' => 'At least one product image is required.',
            'images.max' => 'You can upload a maximum of 5 images.',
            
            'images.*.required' => 'Each image file is required.',
            'images.*.image' => 'Each file must be a valid image.',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, or GIF format.',
            'images.*.max' => 'Each image must not exceed 2MB.',
            'images.*.dimensions' => 'Images must be between 100x100 and 4000x4000 pixels.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize string inputs
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->name)]);
        }
        
        if ($this->has('description')) {
            $this->merge(['description' => trim($this->description)]);
        }
        
        if ($this->has('additional_info')) {
            $this->merge(['additional_info' => trim($this->additional_info)]);
        }
        
        // Ensure price is properly formatted
        if ($this->has('price')) {
            $this->merge(['price' => (float) $this->price]);
        }
        
        // Ensure category_id is integer
        if ($this->has('category_id')) {
            $this->merge(['category_id' => (int) $this->category_id]);
        }
    }
}
