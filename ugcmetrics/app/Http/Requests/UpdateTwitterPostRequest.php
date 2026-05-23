<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTwitterPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'sometimes|string',
            'likes' => 'sometimes|integer|min:0',
            'comments' => 'sometimes|integer|min:0',
            'retweets' => 'sometimes|integer|min:0',
            'engagement_rate' => 'sometimes|numeric|between:0,100',
        ];
    }
}