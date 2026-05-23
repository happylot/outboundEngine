<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTwitterReportsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_profile_id' => 'required|exists:social_profiles,id',
            'year' => 'required|integer|min:2000|max:'.(date('Y')+1),
            'month' => 'required|integer|min:1|max:12',
            'followers_start' => 'nullable|integer|min:0',
            'followers_end' => 'nullable|integer|min:0',
            'tweets_count' => 'nullable|integer|min:0',
            'engagement_rate' => 'nullable|numeric|between:0,100',
        ];
    }
}