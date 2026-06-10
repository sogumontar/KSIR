<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;

#[Layout('components.layouts.admin')]
#[Title('User Management - Inventory Pro')]
class UserManagement extends Component
{
    use WithPagination, WithFileUploads;

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
    public string $editPassword = '';
    public string $editPassword_confirmation = '';
    public $editPhoto;
    public ?string $existingPhoto = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAdd()
    {
        $this->reset(['editingUserId', 'editName', 'editEmail', 'editPhone', 'editRole', 'editStatus', 'editPassword', 'editPassword_confirmation', 'editPhoto', 'existingPhoto']);
        $this->editRole = 'staff';
        $this->editStatus = true;
        $this->showEditSidebar = true;
    }

    public function openEdit(int $userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $userId;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editPhone = $user->phone_number ?? '';
        $this->editRole = $user->is_admin ? 'admin' : 'staff';
        $this->editStatus = $user->status === 'active';
        $this->editPassword = '';
        $this->editPassword_confirmation = '';
        $this->existingPhoto = $user->photo_path;
        $this->editPhoto = null;
        $this->showEditSidebar = true;
    }

    public function saveEdit()
    {
        $isAdd = $this->editingUserId === null;

        $rules = [
            'editName' => 'required|string|max:255',
            'editEmail' => ['required', 'email', 'max:255', $isAdd
                ? 'unique:users,email'
                : 'unique:users,email,' . $this->editingUserId],
            'editPhone' => 'nullable|string|max:20',
            'editRole' => 'required|in:admin,staff',
            'editPhoto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if ($isAdd) {
            $rules['editPassword'] = 'required|min:8|confirmed';
            $rules['editPassword_confirmation'] = 'required';
        } else {
            $rules['editPassword'] = 'nullable|min:8|confirmed';
            $rules['editPassword_confirmation'] = 'nullable';
        }

        $this->validate($rules);

        $photoPath = $this->existingPhoto;
        if ($this->editPhoto) {
            if ($photoPath) {
                \Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $this->editPhoto->store('users', 'public');
        } elseif (!$this->existingPhoto && $photoPath) {
            \Storage::disk('public')->delete($photoPath);
            $photoPath = null;
        }

        $statusString = $this->editStatus ? 'active' : 'inactive';
        $isAdmin = $this->editRole === 'admin';

        if ($isAdd) {
            User::create([
                'name' => $this->editName,
                'email' => $this->editEmail,
                'phone_number' => $this->editPhone,
                'password' => $this->editPassword,
                'is_admin' => $isAdmin,
                'status' => $statusString,
                'photo_path' => $photoPath,
            ]);
        } else {
            $user = User::findOrFail($this->editingUserId);
            $updateData = [
                'name' => $this->editName,
                'email' => $this->editEmail,
                'phone_number' => $this->editPhone,
                'is_admin' => $isAdmin,
                'status' => $statusString,
                'photo_path' => $photoPath,
            ];

            if ($this->editPassword) {
                $updateData['password'] = $this->editPassword;
            }

            $user->update($updateData);
        }

        $this->showEditSidebar = false;
        $this->editingUserId = null;
    }

    public function cancelEdit()
    {
        $this->showEditSidebar = false;
        $this->editingUserId = null;
    }

    public function deleteUser(int $userId)
    {
        $user = User::findOrFail($userId);
        if ($user->photo_path) {
            \Storage::disk('public')->delete($user->photo_path);
        }
        $user->delete();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    protected function validationAttributes()
    {
        return [
            'editName' => 'name',
            'editEmail' => 'email',
            'editPhone' => 'phone number',
            'editRole' => 'role',
            'editPassword' => 'password',
            'editPassword_confirmation' => 'password confirmation',
            'editPhoto' => 'photo',
        ];
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%'))
            ->when($this->roleFilter === 'admin', fn($q) => $q->where('is_admin', true))
            ->when($this->roleFilter === 'staff', fn($q) => $q->where('is_admin', false))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}