<?php

namespace App\Livewire\Seeding;

use App\Models\SeedingScenario;
use App\Models\SeedingTemplate;
use App\Services\AIService;
use Livewire\Component;

class SeedingScenarios extends Component
{
    public ?int $selectedScenarioId = null;

    // Form inputs
    public string $title = '';
    public string $target_link = '';
    public string $brand_name = '';
    public string $context = '';

    // Generating status
    public bool $isGenerating = false;

    public function selectScenario(int $scenarioId): void
    {
        $this->selectedScenarioId = $scenarioId;
        $this->resetValidation();
    }

    public function createScenario(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'target_link' => 'required|url',
            'brand_name' => 'required|string|max:255',
            'context' => 'required|string',
        ]);

        $scenario = SeedingScenario::create([
            'title' => $this->title,
            'target_link' => $this->target_link,
            'brand_name' => $this->brand_name,
            'context' => $this->context,
        ]);

        $this->reset(['title', 'target_link', 'brand_name', 'context']);
        $this->selectedScenarioId = $scenario->id;

        // Auto generate templates for the new scenario
        $this->generateScriptsForScenario($scenario->id);
        
        session()->flash('scenario_success', 'Seeding scenario created and AI scripts generated successfully.');
    }

    public function generateScriptsForScenario(int $scenarioId): void
    {
        $scenario = SeedingScenario::find($scenarioId);
        if (!$scenario) return;

        $this->isGenerating = true;

        // Clear existing templates
        $scenario->templates()->delete();

        // Call AIService
        $comments = AIService::generateSeedingComments($scenario);

        // Store generated templates
        foreach ($comments as $angle => $list) {
            foreach ($list as $commentText) {
                SeedingTemplate::create([
                    'scenario_id' => $scenario->id,
                    'angle' => $angle,
                    'content' => $commentText,
                ]);
            }
        }

        $this->isGenerating = false;
        session()->flash('scripts_success', 'AI Seeding scripts generated successfully using psychological angles.');
    }

    public function deleteScenario(int $scenarioId): void
    {
        $scenario = SeedingScenario::find($scenarioId);
        if ($scenario) {
            $scenario->delete();
            if ($this->selectedScenarioId === $scenarioId) {
                $this->selectedScenarioId = null;
            }
            session()->flash('scenario_success', 'Scenario deleted.');
        }
    }

    public function render()
    {
        $scenarios = SeedingScenario::latest()->get();
        $selectedScenario = $this->selectedScenarioId 
            ? SeedingScenario::with('templates')->find($this->selectedScenarioId) 
            : null;

        return view('livewire.seeding.seeding-scenarios', [
            'scenarios' => $scenarios,
            'selectedScenario' => $selectedScenario,
        ])->layout('components.layouts.app', ['title' => 'Seeding Scripts Template']);
    }
}
