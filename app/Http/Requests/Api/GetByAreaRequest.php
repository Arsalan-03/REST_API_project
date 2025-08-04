<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetByAreaRequest extends FormRequest
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
            'min_lat' => 'required|numeric|between:-90,90',
            'max_lat' => 'required|numeric|between:-90,90',
            'min_lng' => 'required|numeric|between:-180,180',
            'max_lng' => 'required|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'min_lat.required' => 'Минимальная широта обязательна для заполнения',
            'min_lat.numeric' => 'Минимальная широта должна быть числом',
            'min_lat.between' => 'Минимальная широта должна быть между -90 и 90',
            'max_lat.required' => 'Максимальная широта обязательна для заполнения',
            'max_lat.numeric' => 'Максимальная широта должна быть числом',
            'max_lat.between' => 'Максимальная широта должна быть между -90 и 90',
            'min_lng.required' => 'Минимальная долгота обязательна для заполнения',
            'min_lng.numeric' => 'Минимальная долгота должна быть числом',
            'min_lng.between' => 'Минимальная долгота должна быть между -180 и 180',
            'max_lng.required' => 'Максимальная долгота обязательна для заполнения',
            'max_lng.numeric' => 'Максимальная долгота должна быть числом',
            'max_lng.between' => 'Максимальная долгота должна быть между -180 и 180',
        ];
    }
} 