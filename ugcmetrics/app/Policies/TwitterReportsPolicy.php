<?php

namespace App\Policies;

use App\Models\TwitterReports;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TwitterReportsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-twitter-reports');
    }

    public function view(User $user, TwitterReports $twitterReports): bool
    {
        return $user->hasPermissionTo('view-twitter-reports') && 
               $user->canAccessSocialProfile($twitterReports->social_profile_id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-twitter-reports');
    }

    public function update(User $user, TwitterReports $twitterReports): bool
    {
        return $user->hasPermissionTo('update-twitter-reports') && 
               $user->canAccessSocialProfile($twitterReports->social_profile_id);
    }

    public function delete(User $user, TwitterReports $twitterReports): bool
    {
        return $user->hasPermissionTo('delete-twitter-reports') && 
               $user->canAccessSocialProfile($twitterReports->social_profile_id);
    }

    public function restore(User $user, TwitterReports $twitterReports): bool
    {
        return $user->hasPermissionTo('restore-twitter-reports') && 
               $user->canAccessSocialProfile($twitterReports->social_profile_id);
    }

    public function forceDelete(User $user, TwitterReports $twitterReports): bool
    {
        return $user->hasPermissionTo('force-delete-twitter-reports') && 
               $user->canAccessSocialProfile($twitterReports->social_profile_id);
    }
}