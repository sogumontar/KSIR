<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryStoreContributor;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.user')]
#[Title('Kelola Kontributor - Laundry')]
class ContributorManager extends Component
{
    public string $inviteName = '';
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public function createInvite(): void
    {
        $this->validate([
            'inviteName' => 'required|string|max:100',
        ]);

        LaundryStoreContributor::create([
            'owner_user_id'    => Auth::id(),
            'invite_name'      => $this->inviteName,
            'contributor_user_id' => null,
            'status'           => 'pending',
        ]);

        $this->inviteName = '';
        session()->flash('contrib_message', 'Undangan berhasil dibuat.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function removeContributor(): void
    {
        if (!$this->deleteId) return;

        $record = LaundryStoreContributor::where('id', $this->deleteId)
            ->where('owner_user_id', Auth::id())
            ->first();

        if ($record) {
            $record->delete();
            session()->flash('contrib_message', 'Kontributor berhasil dihapus.');
        }

        $this->deleteId = null;
        $this->showDeleteConfirm = false;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->showDeleteConfirm = false;
    }

    public function render()
    {
        $contributors = LaundryStoreContributor::where('owner_user_id', Auth::id())
            ->with('contributor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.laundry.contributor-manager', [
            'contributors' => $contributors,
        ]);
    }
}
