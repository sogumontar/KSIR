<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('components.layouts.customer')]
#[Title('My Profile - Inventory Pro')]
class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';
    public $photo;

    public bool $profileSaved = false;
    public bool $passwordSaved = false;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number ?? '';
    }

    public function updateProfile()
    {
        $this->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|max:20',
            'photo'        => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $data = [
            'name'         => $this->name,
            'email'        => $this->email,
            'phone_number' => $this->phone_number,
        ];

        if ($this->photo) {
            $path = $this->photo->store('avatars', 'public');
            $data['photo_path'] = $path;
        }

        $user->update($data);

        $this->photo = null;
        $this->profileSaved = true;
        $this->dispatch('profile-saved');
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword'         => 'required',
            'newPassword'             => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'The current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->passwordSaved = true;
    }

    public function render()
    {
        return view('livewire.customer.profile');
    }
}
