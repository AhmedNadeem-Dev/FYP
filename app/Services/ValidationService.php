<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidationService
{
    public static function validateProduct(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'category_id' => 'required|exists:categories,category_id',
            'additional_info' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    }

    public static function validateUser(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'phone' => 'required|string|regex:/^[0-9+\-\s()]+$/|max:20',
            'role' => [
                'required',
                Rule::in(['admin', 'scrapSeller', 'artist'])
            ]
        ]);
    }

    public static function validateCustomRequest(array $data)
    {
        return Validator::make($data, [
            'description' => 'required|string|max:2000',
            'materials' => 'required|string|max:500',
            'dimensions' => 'nullable|string|max:255',
            'style_preferences' => 'nullable|string|max:500',
            'budget' => 'required|numeric|min:0|max:999999.99',
            'deadline' => 'required|date|after:today',
            'artist_expertise' => 'nullable|string|max:500',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);
    }

    public static function validateOrder(array $data)
    {
        return Validator::make($data, [
            'shipping_address' => 'required|string|max:500',
            'contact_phone' => 'required|string|regex:/^[0-9+\-\s()]+$/|max:20',
            'payment_method' => 'required|string|in:cash_on_delivery,bank_transfer,credit_card',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1|max:100'
        ]);
    }
} 