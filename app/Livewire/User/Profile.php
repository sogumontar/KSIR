<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.user')]
#[Title('My Profile - Inventory Pro')]
class Profile extends Component
{
    use WithFileUploads;

    public string $phoneNumber = '';
    public $photo;
    public ?string $existingPhoto = null;

    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public bool $showPasswordForm = false;

    public function mount()
    {
        $user = auth()->user();
        $this->phoneNumber = $user->phone_number ?? '';
        $this->existingPhoto = $user->photo_path;
    }

    public function updateProfile()
    {
        $this->validate([
            'phoneNumber' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();
        $photoPath = $this->existingPhoto;

        if ($this->photo) {
            if ($photoPath) {
                \Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $this->photo->store('users', 'public');
        } elseif (!$this->existingPhoto && $photoPath) {
            \Storage::disk('public')->delete($photoPath);
            $photoPath = null;
        }

        $user->update([
            'phone_number' => $this->phoneNumber,
            'photo_path' => $photoPath,
        ]);

        $this->existingPhoto = $photoPath;
        $this->photo = null;
        session()->flash('message', 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required|current_password',
            'newPassword' => 'required|min:8|confirmed',
            'newPasswordConfirmation' => 'required',
        ]);

        auth()->user()->update([
            'password' => $this->newPassword,
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation', 'showPasswordForm']);
        session()->flash('message', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.user.profile');
    }
}