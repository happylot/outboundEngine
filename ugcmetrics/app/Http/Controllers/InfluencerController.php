<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use App\Models\Platform;
use App\Models\SocialProfile;
use App\Http\Requests\StoreInfluencerRequest;
use App\Http\Requests\UpdateInfluencerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InfluencerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all influencers with their social profiles
        $influencers = Influencer::with('socialProfiles')->get();

        // Return the view with the influencers data
        return view('influencer.index', compact('influencers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all platforms for the dropdown
        $platforms = Platform::all();
        
        // Return the view for creating a new influencer with platforms
        return view('influencer.create', compact('platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInfluencerRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Process basic influencer data
            $influencerData = $request->validated();
            
            // Handle profile picture upload if present
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('influencers', 'public');
                $influencerData['profile_picture_url'] = Storage::url($path);
            }else{
                $influencerData['profile_picture_url'] = ''; // Set to null if no file is uploaded
            }
            // Create the influencer
            $influencer = Influencer::create($influencerData);
            
            // Process the first social profile
            if ($request->filled('social_username') && $request->filled('platform_id')) {
                $this->createSocialProfile(
                    $influencer->id,
                    $request->platform_id,
                    $request->social_username
                );
            }
            
            // Process additional social profiles
            if ($request->has('social_usernames') && $request->has('platform_ids')) {
                $usernames = $request->social_usernames;
                $platformIds = $request->platform_ids;
                
                foreach ($usernames as $index => $username) {
                    if (!empty($username) && !empty($platformIds[$index])) {
                        $this->createSocialProfile(
                            $influencer->id,
                            $platformIds[$index],
                            $username,

                        );
                    }
                }
            }
            
            DB::commit();
            
            // Redirect to the influencer index with a success message
            return redirect()->route('influencer.index')
                ->with('success', 'Influencer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Influencer $influencer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Influencer $influencer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInfluencerRequest $request, Influencer $influencer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Influencer $influencer)
    {
        //
    }
    
    /**
     * Create a social profile for an influencer
     */
    private function createSocialProfile($influencerId, $platformId, $username)
    {
        $platform = Platform::find($platformId);
        
        if (!$platform) {
            return false;
        }
        
        return SocialProfile::create([
            'influencer_id' => $influencerId,
            'platform_id' => $platformId,
            'username' => $username,
            'profile_url' => $platform->base_url . $username,
            'followers_count' => 0, // Default value
            'engagement_rate' => 0, // Default value
        ]);
    }
}
