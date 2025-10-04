<?php

namespace App\Livewire;

use App\Models\AdBox;
use App\Models\CodeView;
use App\Models\TriviaCode;
use Livewire\Component;

class HomePage extends Component
{
    public string $code = '';
    public ?TriviaCode $triviaCode = null;
    public bool $showModal = false;
    public string $error = '';

    public function submit()
    {
        $this->error = '';
        $this->triviaCode = null;

        if (empty($this->code)) {
            $this->error = 'Please enter a code';
            return;
        }

        $this->triviaCode = TriviaCode::where('code', $this->code)
            ->where('is_active', true)
            ->with('answers')
            ->first();

        if (!$this->triviaCode) {
            $this->error = 'Invalid code. Please try again.';
            return;
        }

        // Track view
        CodeView::create([
            'trivia_code_id' => $this->triviaCode->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now(),
        ]);

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->code = '';
    }

    public function trackAdClick($adId)
    {
        $ad = AdBox::find($adId);
        if ($ad) {
            $ad->incrementClicks();
        }
    }

    public function render()
    {
        $adBoxes = AdBox::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('livewire.home-page', [
            'adBoxes' => $adBoxes,
        ])->layout('components.layouts.app');
    }
}
