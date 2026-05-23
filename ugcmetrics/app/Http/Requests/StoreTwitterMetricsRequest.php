<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTwitterMetricsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_profile_id' => 'required|exists:social_profiles,id',
            'followers_count' => 'required|integer|min:0',
            'following_count' => 'required|integer|min:0',
            'tweet_count' => 'required|integer|min:0',
            'listed_count' => 'required|integer|min:0',
            'engagement_rate' => 'required|numeric|between:0,100',
        ];
    }
}