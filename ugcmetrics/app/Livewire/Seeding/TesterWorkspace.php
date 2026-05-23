<?php

namespace App\Livewire\Seeding;

use App\Models\SeederAccount;
use App\Models\SeedingLog;
use App\Models\SeedingIncident;
use Livewire\Component;

class TesterWorkspace extends Component
{
    public ?int $selectedAccountId = null;

    // Form fields
    public string $action_type = 'comment';
    public string $target_url = '';
    public string $content_type = 'hand-written-short';
    public string $content = '';
    public string $log_status = 'success';
    
    // Checkpoint details
    public string $incident_description = '';
    public string $checkpoint_resolve_notes = '';

    // Lead capture form fields
    public string $lead_name = '';
    public string $lead_phone = '';
    public string $lead_email = '';
    public string $lead_uid = '';
    public string $lead_profile_url = '';
    public string $lead_comment_url = '';
    public string $lead_content = '';

    public function selectAccount(int $accountId): void
    {
        $this->selectedAccountId = $accountId;
        $this->resetValidation();
        $this->reset(['target_url', 'content', 'log_status', 'incident_description', 'lead_name', 'lead_phone', 'lead_email', 'lead_uid', 'lead_profile_url', 'lead_comment_url', 'lead_content']);
    }

    public function submitLog(): void
    {
        $this->validate([
            'selectedAccountId' => 'required|exists:seeder_accounts,id',
            'action_type' => 'required|string',
            'log_status' => 'required|string',
            'target_url' => 'nullable|url',
        ]);

        $account = SeederAccount::find($this->selectedAccountId);
        if (!$account) {
            return;
        }

        // 1. Create Log Entry
        $log = SeedingLog::create([
            'seeder_account_id' => $account->id,
            'action_type' => $this->action_type,
            'target_url' => $this->target_url,
            'content_type' => $this->content_type,
            'content' => $this->content,
            'status' => $this->log_status,
            'logged_at' => now(),
        ]);

        // 2. Process account state changes
        if ($this->log_status === 'checkpoint_triggered') {
            // Log Checkpoint Incident
            SeedingIncident::create([
                'seeder_account_id' => $account->id,
                'incident_type' => 'checkpoint',
                'trigger_action_id' => $log->id,
                'description' => $this->incident_description ?: 'Triggered checkpoint during ' . $this->action_type,
            ]);

            $account->update([
                'status' => 'checkpoint',
                'cooldown_until' => null,
                'last_active_at' => now(),
            ]);

            session()->flash('log_error', 'Checkpoint reported. Account status updated to Checkpoint.');
        } else {
            // Success or Warning: Calculate Cooldown
            $cooldownMinutes = 0;
            if ($account->behaviorGroup) {
                $cooldownMinutes = rand(
                    $account->behaviorGroup->min_interval_minutes, 
                    $account->behaviorGroup->max_interval_minutes
                );
            }

            $cooldownUntil = $cooldownMinutes > 0 ? now()->addMinutes($cooldownMinutes) : null;
            $newStatus = $cooldownMinutes > 0 ? 'cooldown' : 'active';

            $account->update([
                'status' => $newStatus,
                'cooldown_until' => $cooldownUntil,
                'last_active_at' => now(),
            ]);

            $cooldownMsg = $cooldownMinutes > 0 ? " cooling down for {$cooldownMinutes} minutes" : "";
            session()->flash('log_success', "Action logged successfully. Account is{$cooldownMsg}.");
        }

        $this->reset(['target_url', 'content', 'log_status', 'incident_description']);
    }

    public function resolveCheckpoint(): void
    {
        $this->validate([
            'selectedAccountId' => 'required|exists:seeder_accounts,id',
        ]);

        $account = SeederAccount::find($this->selectedAccountId);
        if (!$account || $account->status !== 'checkpoint') {
            return;
        }

        // Find active incident
        $incident = SeedingIncident::where('seeder_account_id', $account->id)
            ->whereNull('resolved_at')
            ->latest()
            ->first();

        if ($incident) {
            $created = $incident->created_at;
            $recoveryTime = now()->diffInMinutes($created);
            
            $incident->update([
                'resolved_at' => now(),
                'recovery_time_minutes' => $recoveryTime,
                'description' => $incident->description . ' | Resolving notes: ' . ($this->checkpoint_resolve_notes ?: 'Manual recovery'),
            ]);
        }

        $account->update([
            'status' => 'active',
            'cooldown_until' => null,
        ]);

        $this->reset('checkpoint_resolve_notes');
        session()->flash('log_success', 'Checkpoint resolved successfully. Account is now Active and ready for tasks.');
    }

    public function submitLead(): void
    {
        $this->validate([
            'selectedAccountId' => 'required|exists:seeder_accounts,id',
            'lead_name' => 'required|string|min:2|max:255',
            'lead_phone' => 'nullable|string|max:50',
            'lead_email' => 'nullable|email|max:255',
            'lead_uid' => 'nullable|string|max:255',
            'lead_profile_url' => 'nullable|url|max:500',
            'lead_comment_url' => 'nullable|url|max:500',
            'lead_content' => 'nullable|string',
        ]);

        $account = SeederAccount::find($this->selectedAccountId);
        if (!$account) {
            return;
        }

        // Create Seeding Lead
        $lead = \App\Models\SeedingLead::create([
            'seeder_account_id' => $account->id,
            'customer_name' => $this->lead_name,
            'customer_uid' => $this->lead_uid,
            'platform' => $account->platform,
            'profile_url' => $this->lead_profile_url,
            'comment_url' => $this->lead_comment_url,
            'content' => $this->lead_content,
            'phone' => $this->lead_phone,
            'email' => $this->lead_email,
            'status' => 'new',
        ]);

        // Trigger sync automatically
        $syncSuccess = $lead->syncToZenify();

        if ($syncSuccess) {
            session()->flash('lead_success', "Lead logged & synced to Zenify.cx (Lead ID: {$lead->zenify_lead_id})");
        } else {
            session()->flash('lead_warning', "Lead logged but failed to sync to Zenify: {$lead->sync_error}");
        }

        // Reset lead form fields
        $this->reset(['lead_name', 'lead_phone', 'lead_email', 'lead_uid', 'lead_profile_url', 'lead_comment_url', 'lead_content']);
    }

    public function render()
    {
        $accounts = SeederAccount::with('behaviorGroup')->get();
        $selectedAccount = $this->selectedAccountId ? SeederAccount::with(['behaviorGroup', 'device'])->find($this->selectedAccountId) : null;

        // Check constraints
        $isCooldown = false;
        $isLimitReached = false;
        $actionsToday = 0;
        $dailyLimit = 0;

        if ($selectedAccount) {
            $isCooldown = $selectedAccount->isUnderCooldown();
            $actionsToday = $selectedAccount->getDailyActionCount();
            
            if ($selectedAccount->behaviorGroup) {
                $dailyLimit = $selectedAccount->behaviorGroup->daily_limit;
                $isLimitReached = $actionsToday >= $dailyLimit;
            }
        }

        return view('livewire.seeding.tester-workspace', [
            'accounts' => $accounts,
            'selectedAccount' => $selectedAccount,
            'isCooldown' => $isCooldown,
            'isLimitReached' => $isLimitReached,
            'actionsToday' => $actionsToday,
            'dailyLimit' => $dailyLimit,
        ])->layout('components.layouts.app', ['title' => 'Tester Workspace']);
    }
}
