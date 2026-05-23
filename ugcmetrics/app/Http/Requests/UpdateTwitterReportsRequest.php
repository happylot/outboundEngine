<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTwitterReportsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar a lógica de autorización real
    }

    public function rules(): array
    {
        return [
            'followers_end' => 'nullable|integer|min:0',
            'tweets_count' => 'nullable|integer|min:0',
            'engagement_rate' => 'nullable|numeric|between:0,100',
        ];
    }
}