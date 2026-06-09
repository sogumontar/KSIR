<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;

#[Layout('components.layouts.admin')]
#[Title('User Management - Inventory Pro')]
class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';

    // Edit sidebar state
    public bool $showEditSidebar = false;
    public ?int $editingUserId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editPhone = '';
    public string $editRole = 'staff';
    public bool $editStatus = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAdd()
    {
        $this->reset(['editingUserId', 'editName', 'editEmail', 'editPhone', 'editRole', 'editStatus']);
        $this->showEditSidebar = true;
    }

    public function openEdit(int $userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $userId;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editPhone = $user->phone ?? '';
        $this->editRole = $user->is_admin ? 'admin' : 'staff';
        $this->editStatus = true; // placeholder
        $this->showEditSidebar = true;
    }

    public function saveEdit()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|max:255',
        ]);

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update([
                'name' => $this->editName,
                'email' => $this->editEmail,
                'is_admin' => $this->editRole === 'admin',
            ]);
        } else {
            User::create([
                'name' => $this->editName,
                'email' => $this->editEmail,
                'password' => bcrypt('password'), // default password for new users
                'is_admin' => $this->editRole === 'admin',
            ]);
        }

        $this->showEditSidebar = false;
        $this->editingUserId = null;
        $this->reset(['editName', 'editEmail', 'editPhone', 'editRole']);
    }

    public function cancelEdit()
    {
        $this->showEditSidebar = false;
        $this->editingUserId = null;
    }

    public function deleteUser(int $userId)
    {
        User::findOrFail($userId)->delete();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}
