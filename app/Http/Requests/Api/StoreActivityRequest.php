<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:activities,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Название деятельности обязательно для заполнения',
            'name.string' => 'Название должно быть строкой',
            'name.max' => 'Название не может быть длиннее 255 символов',
            'parent_id.integer' => 'ID родительской деятельности должен быть числом',
            'parent_id.exists' => 'Указанная родительская деятельность не существует',
        ];
    }
} 