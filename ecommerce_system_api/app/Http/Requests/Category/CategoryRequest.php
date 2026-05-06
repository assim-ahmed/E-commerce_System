<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Only admin can create/update/delete
        if ($this->isMethod('POST') || $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->isMethod('DELETE')) {
            return $user && $user->isAdmin();
        }
        
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['slug'] = 'nullable|string|max:255|unique:categories,slug,' . $this->route('category');
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'slug.unique' => 'This slug is already used',
        ];
    }
}