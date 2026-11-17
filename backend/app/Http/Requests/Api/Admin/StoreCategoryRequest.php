<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
   
        $categoryParam = $this->route('category');
        $categoryId = $categoryParam instanceof \App\Models\Category ? $categoryParam->id : $categoryParam;

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . ($isUpdate ? $categoryId : 'NULL'),
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_logo' => 'sometimes|boolean',
        ];
    }
}
