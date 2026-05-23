<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTwitterPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_profile_id' => 'required|exists:social_profiles,id',
            'post_id' => 'required|string|max:255|unique:twitter_posts,post_id',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'likes' => 'required|integer|min:0',
            'comments' => 'required|integer|min:0',
            'retweets' => 'required|integer|min:0',
            'engagement_rate' => 'required|numeric|between:0,100',
        ];
    }
}