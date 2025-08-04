<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchWithFiltersRequest extends FormRequest
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
            'name' => 'nullable|string|min:2',
            'building_id' => 'nullable|integer|exists:buildings,id',
            'activity_id' => 'nullable|integer|exists:activities,id',
            'sort_by' => 'nullable|string|in:name,created_at',
            'sort_order' => 'nullable|string|in:asc,desc',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Название должно быть строкой',
            'name.min' => 'Название должно содержать минимум 2 символа',
            'building_id.integer' => 'ID здания должен быть числом',
            'building_id.exists' => 'Указанное здание не существует',
            'activity_id.integer' => 'ID деятельности должен быть числом',
            'activity_id.exists' => 'Указанная деятельность не существует',
            'sort_by.string' => 'Поле сортировки должно быть строкой',
            'sort_by.in' => 'Сортировка может быть только по name или created_at',
            'sort_order.string' => 'Порядок сортировки должен быть строкой',
            'sort_order.in' => 'Порядок сортировки может быть только asc или desc',
        ];
    }
} 