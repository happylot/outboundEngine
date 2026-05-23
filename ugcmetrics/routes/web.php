<?php

use App\Http\Controllers\InfluencerController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedditController;
use App\Http\Controllers\{
    InstagramGetInstagramData,
    InstagramMetricsController,
    SocialProfileController,
    TwitterGetTwitterData,
    TwitterMetricsController
};
use App\Livewire\Seeding\SeedingDashboard;
use App\Livewire\Seeding\TesterWorkspace;
use App\Livewire\Seeding\SeedingScenarios;
use App\Livewire\Seeding\SeedingLeads;
use App\Livewire\Outbound\SalesBrain;
use App\Livewire\Outbound\OutboundLeads;
use App\Livewire\Outbound\ResearchWorkspace;
use App\Livewire\Outbound\ReactivationEngine;



Route::get('/', [InfluencerController::class, 'index'])->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Seeding Marketing Stage 1 Routes
    Route::get('seeding/dashboard', SeedingDashboard::class)->name('seeding.dashboard');
    Route::get('seeding/workspace', TesterWorkspace::class)->name('seeding.workspace');
    Route::get('seeding/scenarios', SeedingScenarios::class)->name('seeding.scenarios');
    Route::get('seeding/leads', SeedingLeads::class)->name('seeding.leads');

    // Outbound Sales Routes
    Route::get('outbound/sales-brain', SalesBrain::class)->name('outbound.sales-brain');
    Route::get('outbound/leads', OutboundLeads::class)->name('outbound.leads');
    Route::get('outbound/research', ResearchWorkspace::class)->name('outbound.research');
    Route::get('outbound/reactivation', ReactivationEngine::class)->name('outbound.reactivation');
});

// Rutas para Instagram
Route::get('instagram', [InstagramMetricsController::class, 'index'])->name('instagram.index');
Route::get('instagram/{id}', [InstagramMetricsController::class, 'show'])->name('instagram.show');

// Rutas para Twitter
Route::get('twitter', [TwitterMetricsController::class, 'index'])->name('twitter.index');
Route::get('twitter/{id}', [TwitterMetricsController::class, 'show'])->name('twitter.show');
Route::get('twitter/fetch-data', TwitterGetTwitterData::class)->name('twitter.fetch-data');

// Rutas para Influencers
Route::get('influencer', [InfluencerController::class, 'index'])->name('influencer.index');
Route::get('influencer/create', [InfluencerController::class, 'create'])->name('influencer.create');
Route::post('influencer', [InfluencerController::class, 'store'])->name('influencer.store');

// Rutas para Social Profiles
Route::get('socialprofile/{socialProfile}', [SocialProfileController::class, 'show'])->name('socialprofile.show');

// Rutas para obtención de datos
Route::get('instagram/get-data', InstagramGetInstagramData::class)->name('instagram.get-data');

// Rutas para Twitch
Route::get('twitch', [App\Http\Controllers\TwitchController::class, 'index'])->name('twitch.index');
Route::get('twitch/{username}', [App\Http\Controllers\TwitchController::class, 'show'])->name('twitch.show');
Route::get('twitch/{username}/fetch', [App\Http\Controllers\TwitchController::class, 'fetch'])->name('twitch.fetch');

//Rutas de reddit
Route::get('/reddit', [RedditController::class, 'index'])->name('reddit.index');
Route::get('/reddit/{username}', [RedditController::class, 'show'])->name('reddit.show');

require __DIR__ . '/auth.php';