<?php

namespace App\Livewire\Outbound;

use Livewire\Component;
use App\Models\OutboundKbEntry;

class SalesBrain extends Component
{
    public $showModal = false;
    
    public $title = '';
    public $type = 'product_info';
    public $content = '';
    public $tags = '';

    protected $rules = [
        'title' => 'required|min:3',
        'type' => 'required|in:sales_deck,objection_handling,product_info,target_persona',
        'content' => 'required|min:10',
    ];

    public function save()
    {
        $this->validate();

        OutboundKbEntry::create([
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content,
            'tags' => $this->tags,
        ]);

        $this->reset(['title', 'type', 'content', 'tags']);
        $this->type = 'product_info';
        $this->showModal = false;
        
        session()->flash('message', 'Knowledge base entry added successfully.');
    }

    public function render()
    {
        return view('livewire.outbound.sales-brain', [
            'entries' => OutboundKbEntry::latest()->get()
        ]);
    }
}
