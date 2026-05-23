<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTwitterMetricsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'followers_count' => 'sometimes|integer|min:0',
            'following_count' => 'sometimes|integer|min:0',
            'tweet_count' => 'sometimes|integer|min:0',
            'listed_count' => 'sometimes|integer|min:0',
            'engagement_rate' => 'sometimes|numeric|between:0,100',
        ];
    }
}