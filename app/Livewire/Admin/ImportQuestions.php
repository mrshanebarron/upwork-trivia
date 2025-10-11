<?php

namespace App\Livewire\Admin;

use App\Services\TriviaApiService;
use Livewire\Component;

class ImportQuestions extends Component
{
    public int $amount = 10;
    public ?string $category = null;
    public ?string $difficulty = null;
    public ?string $startDate = null;

    public bool $importing = false;
    public ?array $result = null;

    protected array $rules = [
        'amount' => 'required|integer|min:1|max:50',
        'category' => 'nullable|string',
        'difficulty' => 'nullable|string',
        'startDate' => 'nullable|date|after:yesterday',
    ];

    public function mount()
    {
        $this->startDate = now()->addDay()->format('Y-m-d');
    }

    public function import(TriviaApiService $triviaService)
    {
        $this->validate();

        $this->importing = true;
        $this->result = null;

        try {
            $startDate = $this->startDate ? \Carbon\Carbon::parse($this->startDate) : null;

            $this->result = $triviaService->importQuestions(
                amount: $this->amount,
                category: $this->category,
                difficulty: $this->difficulty,
                startDate: $startDate
            );

            if ($this->result['success']) {
                session()->flash('success', $this->result['message']);
            } else {
                session()->flash('error', $this->result['message']);
            }
        } catch (\Exception $e) {
            $this->result = [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'imported' => 0,
            ];
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        } finally {
            $this->importing = false;
        }
    }

    public function render(TriviaApiService $triviaService)
    {
        return view('livewire.admin.import-questions', [
            'categories' => $triviaService->getCategories(),
            'difficulties' => $triviaService->getDifficulties(),
        ])->layout('layouts.app');
    }
}
