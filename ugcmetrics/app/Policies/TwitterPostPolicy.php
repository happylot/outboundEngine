<?php

namespace App\Policies;

use App\Models\TwitterPost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TwitterPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-twitter-posts');
    }

    public function view(User $user, TwitterPost $twitterPost): bool
    {
        return $user->hasPermissionTo('view-twitter-posts') && 
               $user->canAccessSocialProfile($twitterPost->social_profile_id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-twitter-posts');
    }

    public function update(User $user, TwitterPost $twitterPost): bool
    {
        return $user->hasPermissionTo('update-twitter-posts') && 
               $user->canAccessSocialProfile($twitterPost->social_profile_id);
    }

    public function delete(User $user, TwitterPost $twitterPost): bool
    {
        return $user->hasPermissionTo('delete-twitter-posts') && 
               $user->canAccessSocialProfile($twitterPost->social_profile_id);
    }

    public function restore(User $user, TwitterPost $twitterPost): bool
    {
        return $user->hasPermissionTo('restore-twitter-posts') && 
               $user->canAccessSocialProfile($twitterPost->social_profile_id);
    }

    public function forceDelete(User $user, TwitterPost $twitterPost): bool
    {
        return $user->hasPermissionTo('force-delete-twitter-posts') && 
               $user->canAccessSocialProfile($twitterPost->social_profile_id);
    }
}