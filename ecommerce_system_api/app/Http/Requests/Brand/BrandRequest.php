<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        
        if ($this->isMethod('POST') || $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->isMethod('DELETE')) {
            return $user && $user->isAdmin();
        }
        
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['slug'] = 'nullable|string|max:255|unique:brands,slug,' . $this->route('brand');
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required',
            'slug.unique' => 'This slug is already used',
        ];
    }
}