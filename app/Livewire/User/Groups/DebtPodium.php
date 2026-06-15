<?php

namespace App\Livewire\User\Groups;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;

#[Layout('components.layouts.user')]
#[Title('Gamified Debt Summary - Inventory Pro')]
class DebtPodium extends Component
{
    public int $groupId;
    public array $debts = [];

    public function mount(int $id)
    {
        $this->groupId = $id;
        $this->fetchDebts();
    }

    public function fetchDebts()
    {
        // For server-side rendering, we can also just call the controller logic directly
        // but the spec asked for an endpoint. We will do a direct internal call to avoid HTTP overhead
        // or just use the endpoint via HTTP facade if we provide full URL, but internal request is safer without full domain.
        $controller = app(\App\Http\Controllers\Api\DebtController::class);
        $response = $controller->index($this->groupId);
        $this->debts = json_decode($response->getContent(), true);
    }

    public function render()
    {
        return view('livewire.user.groups.debt-podium');
    }
}
