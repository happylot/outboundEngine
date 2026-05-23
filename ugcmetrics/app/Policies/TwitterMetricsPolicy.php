<?php

namespace App\Policies;

use App\Models\TwitterMetrics;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TwitterMetricsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-twitter-metrics');
    }

    public function view(User $user, TwitterMetrics $twitterMetrics): bool
    {
        return $user->hasPermissionTo('view-twitter-metrics') && 
               $user->canAccessSocialProfile($twitterMetrics->social_profile_id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-twitter-metrics');
    }

    public function update(User $user, TwitterMetrics $twitterMetrics): bool
    {
        return $user->hasPermissionTo('update-twitter-metrics') && 
               $user->canAccessSocialProfile($twitterMetrics->social_profile_id);
    }

    public function delete(User $user, TwitterMetrics $twitterMetrics): bool
    {
        return $user->hasPermissionTo('delete-twitter-metrics') && 
               $user->canAccessSocialProfile($twitterMetrics->social_profile_id);
    }

    public function restore(User $user, TwitterMetrics $twitterMetrics): bool
    {
        return $user->hasPermissionTo('restore-twitter-metrics') && 
               $user->canAccessSocialProfile($twitterMetrics->social_profile_id);
    }

    public function forceDelete(User $user, TwitterMetrics $twitterMetrics): bool
    {
        return $user->hasPermissionTo('force-delete-twitter-metrics') && 
               $user->canAccessSocialProfile($twitterMetrics->social_profile_id);
    }
}