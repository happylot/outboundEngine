<?php

namespace App\Livewire\Outbound;

use Livewire\Component;
use App\Models\OutboundLead;
use App\Models\SeedingLead;

class OutboundLeads extends Component
{
    public $activeTab = 'outbound';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.outbound.outbound-leads', [
            'outboundLeads' => OutboundLead::latest()->get(),
            'seedingLeads' => SeedingLead::with('seederAccount')->latest()->get()
        ]);
    }
}
