<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.min' => 'Rating must be at least 1',
            'rating.max' => 'Rating cannot exceed 5',
            'comment.max' => 'Comment cannot exceed 1000 characters',
            'images.max' => 'You can upload up to 5 images',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Images must be jpg, jpeg, or png',
            'images.*.max' => 'Each image cannot exceed 2MB',
        ];
    }
}