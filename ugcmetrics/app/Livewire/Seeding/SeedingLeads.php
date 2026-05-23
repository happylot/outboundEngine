<?php

namespace App\Livewire\Seeding;

use App\Models\SeedingLead;
use Livewire\Component;
use Livewire\WithPagination;

class SeedingLeads extends Component
{
    use WithPagination;

    public string $filterPlatform = 'all';
    public string $filterStatus = 'all';
    public string $search = '';

    protected $queryString = [
        'filterPlatform' => ['except' => 'all'],
        'filterStatus' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPlatform(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Manually trigger sync for a specific lead.
     */
    public function syncLead(int $leadId): void
    {
        $lead = SeedingLead::find($leadId);
        if (!$lead) {
            session()->flash('sync_error', 'Lead not found.');
            return;
        }

        $success = $lead->syncToZenify();

        if ($success) {
            session()->flash('sync_success', "Lead #{$lead->id} (Customer: {$lead->customer_name}) successfully synced to Zenify.cx!");
        } else {
            session()->flash('sync_error', "Sync failed for Lead #{$lead->id}: {$lead->sync_error}");
        }
    }

    /**
     * Delete a lead record.
     */
    public function deleteLead(int $leadId): void
    {
        $lead = SeedingLead::find($leadId);
        if ($lead) {
            $lead->delete();
            session()->flash('sync_success', 'Lead record deleted.');
        }
    }

    public function render()
    {
        $query = SeedingLead::with('seederAccount')->latest();

        if ($this->filterPlatform !== 'all') {
            $query->where('platform', $this->filterPlatform);
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_uid', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        $leads = $query->paginate(15);

        return view('livewire.seeding.seeding-leads', [
            'leads' => $leads,
        ])->layout('components.layouts.app', ['title' => 'Mini Leads CRM']);
    }
}
