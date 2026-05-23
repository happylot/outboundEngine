<?php

namespace App\Http\Controllers;

use App\Models\TwitterMetrics;
use App\Models\Influencer;
use App\Models\SocialProfile;
use App\Models\Platform;

class TwitterMetricsController extends Controller
{
    public function index()
    {
        $influencersIds = SocialProfile::select('influencer_id')
            ->where('platform_id', Platform::where('name','Twitter')->first()->id)
            ->get();
            
        $influencers = Influencer::whereIn('id', $influencersIds)->get();
        
        return view('twitter_metrics.index', compact('influencers'));
    }

    public function show(int $id)
    {
        $twitterMetrics = TwitterMetrics::where('social_profile_id', $id)->get();
        return view('twitter_metrics.show', compact('twitterMetrics'));
    }
}