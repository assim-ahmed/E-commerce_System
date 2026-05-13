<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class ApproveReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('sanctum')->user();
        return $user && $user->role === 'admin';
    }

    public function rules(): array
    {
        return [];
    }
}