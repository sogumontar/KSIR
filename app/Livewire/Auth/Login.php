<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.guest')]
#[Title('Login - Inventory Pro')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function authenticate()
    {
        $this->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $credentials = [
            filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            $user = Auth::user();

            if ($user->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->role === 'customer') {
                return redirect()->intended(route('customer.dashboard'));
            }

            return redirect()->intended(route('user.dashboard'));
        }

        $this->addError('email', trans('auth.failed'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
