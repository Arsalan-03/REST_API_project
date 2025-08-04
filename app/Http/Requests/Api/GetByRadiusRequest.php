<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetByRadiusRequest extends FormRequest
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
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:0.1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'latitude.required' => 'Широта обязательна для заполнения',
            'latitude.numeric' => 'Широта должна быть числом',
            'latitude.between' => 'Широта должна быть между -90 и 90',
            'longitude.required' => 'Долгота обязательна для заполнения',
            'longitude.numeric' => 'Долгота должна быть числом',
            'longitude.between' => 'Долгота должна быть между -180 и 180',
            'radius.required' => 'Радиус обязателен для заполнения',
            'radius.numeric' => 'Радиус должен быть числом',
            'radius.min' => 'Радиус должен быть не менее 0.1',
            'radius.max' => 'Радиус должен быть не более 100',
        ];
    }
} 