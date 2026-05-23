<?php

namespace App\Livewire\Seeding;

use App\Models\SeedingDevice;
use App\Models\SeedingBehaviorGroup;
use App\Models\SeederAccount;
use App\Models\SeedingLog;
use App\Models\SeedingIncident;
use Livewire\Component;
use Livewire\WithPagination;

class SeedingDashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'stats';

    // Device Form Properties
    public string $device_name = '';
    public string $device_model = '';
    public string $os_version = '';
    public string $connection_type = 'Wi-Fi';
    public string $proxy_info = '';
    public string $device_notes = '';

    // Behavior Group Form Properties
    public string $group_name = '';
    public int $daily_limit = 5;
    public int $min_interval_minutes = 60;
    public int $max_interval_minutes = 240;
    public string $group_description = '';

    // Seeder Account Form Properties
    public string $account_username = '';
    public string $account_platform = 'facebook';
    public string $account_profile_url = '';
    public string $account_age_group = '< 6 months';
    public ?int $account_device_id = null;
    public ?int $account_behavior_group_id = null;
    public string $account_notes = '';

    protected $queryString = ['activeTab'];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function createDevice(): void
    {
        $this->validate([
            'device_name' => 'required|string|max:255',
            'device_model' => 'required|string|max:255',
            'os_version' => 'required|string|max:255',
            'connection_type' => 'required|string',
            'proxy_info' => 'nullable|string',
        ]);

        SeedingDevice::create([
            'device_name' => $this->device_name,
            'device_model' => $this->device_model,
            'os_version' => $this->os_version,
            'connection_type' => $this->connection_type,
            'proxy_info' => $this->proxy_info,
            'notes' => $this->device_notes,
        ]);

        $this->reset(['device_name', 'device_model', 'os_version', 'connection_type', 'proxy_info', 'device_notes']);
        session()->flash('device_success', 'Device registered successfully.');
    }

    public function createBehaviorGroup(): void
    {
        $this->validate([
            'group_name' => 'required|string|max:255',
            'daily_limit' => 'required|integer|min:1',
            'min_interval_minutes' => 'required|integer|min:0',
            'max_interval_minutes' => 'required|integer|min:0|gte:min_interval_minutes',
        ]);

        SeedingBehaviorGroup::create([
            'name' => $this->group_name,
            'daily_limit' => $this->daily_limit,
            'min_interval_minutes' => $this->min_interval_minutes,
            'max_interval_minutes' => $this->max_interval_minutes,
            'description' => $this->group_description,
        ]);

        $this->reset(['group_name', 'daily_limit', 'min_interval_minutes', 'max_interval_minutes', 'group_description']);
        session()->flash('group_success', 'Behavior testing group created successfully.');
    }

    public function createAccount(): void
    {
        $this->validate([
            'account_username' => 'required|string|max:255',
            'account_platform' => 'required|string',
            'account_age_group' => 'required|string',
        ]);

        SeederAccount::create([
            'username' => $this->account_username,
            'platform' => $this->account_platform,
            'profile_url' => $this->account_profile_url,
            'age_group' => $this->account_age_group,
            'device_id' => $this->account_device_id ?: null,
            'behavior_group_id' => $this->account_behavior_group_id ?: null,
            'status' => 'active',
        ]);

        $this->reset(['account_username', 'account_platform', 'account_profile_url', 'account_age_group', 'account_device_id', 'account_behavior_group_id', 'account_notes']);
        session()->flash('account_success', 'Seeder account created successfully.');
    }

    public function changeAccountStatus(int $accountId, string $newStatus): void
    {
        $account = SeederAccount::find($accountId);
        if ($account) {
            $account->update(['status' => $newStatus]);
            if ($newStatus === 'active') {
                $account->update(['cooldown_until' => null]);
            }
        }
    }

    public function render()
    {
        // Statistics Calculations
        $totalAccounts = SeederAccount::count();
        $activeAccounts = SeederAccount::where('status', 'active')->count();
        $cooldownAccounts = SeederAccount::where('status', 'cooldown')->count();
        $checkpointAccounts = SeederAccount::where('status', 'checkpoint')->count();
        $deadAccounts = SeederAccount::where('status', 'dead')->count();

        $totalLogs = SeedingLog::count();
        $successLogs = SeedingLog::where('status', 'success')->count();
        $checkpointLogs = SeedingLog::where('status', 'checkpoint_triggered')->count();
        $warningLogs = SeedingLog::where('status', 'warning')->count();

        $successRate = $totalLogs > 0 ? round(($successLogs / $totalLogs) * 100, 1) : 0;
        $checkpointRate = $totalLogs > 0 ? round(($checkpointLogs / $totalLogs) * 100, 1) : 0;

        // Incident metrics by platform
        $incidentsByPlatform = SeederAccount::join('seeding_incidents', 'seeder_accounts.id', '=', 'seeding_incidents.seeder_account_id')
            ->selectRaw('seeder_accounts.platform, count(seeding_incidents.id) as total')
            ->groupBy('seeder_accounts.platform')
            ->pluck('total', 'platform')
            ->toArray();

        // Incident rate by age group
        $incidentsByAgeGroup = SeederAccount::join('seeding_incidents', 'seeder_accounts.id', '=', 'seeding_incidents.seeder_account_id')
            ->selectRaw('seeder_accounts.age_group, count(seeding_incidents.id) as total')
            ->groupBy('seeder_accounts.age_group')
            ->pluck('total', 'age_group')
            ->toArray();

        // Lists
        $devices = SeedingDevice::all();
        $behaviorGroups = SeedingBehaviorGroup::all();
        $accounts = SeederAccount::with(['device', 'behaviorGroup'])->get();
        $recentLogs = SeedingLog::with('seederAccount')->latest()->take(10)->get();
        
        $incidents = SeedingIncident::with(['seederAccount', 'triggerAction'])
            ->latest()
            ->paginate(15);

        return view('livewire.seeding.seeding-dashboard', [
            'totalAccounts' => $totalAccounts,
            'activeAccounts' => $activeAccounts,
            'cooldownAccounts' => $cooldownAccounts,
            'checkpointAccounts' => $checkpointAccounts,
            'deadAccounts' => $deadAccounts,
            'successRate' => $successRate,
            'checkpointRate' => $checkpointRate,
            'warningLogs' => $warningLogs,
            'incidentsByPlatform' => $incidentsByPlatform,
            'incidentsByAgeGroup' => $incidentsByAgeGroup,
            'devices' => $devices,
            'behaviorGroups' => $behaviorGroups,
            'accounts' => $accounts,
            'recentLogs' => $recentLogs,
            'incidents' => $incidents,
        ])->layout('components.layouts.app', ['title' => 'Seeding Dashboard']);
    }
}
