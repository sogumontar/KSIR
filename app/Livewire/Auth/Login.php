<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.guest')]
#[Title('Login | Inventory Pro')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount()
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin) {
                return redirect()->to(route('admin.dashboard'));
            }
            return redirect()->to(route('user.dashboard'));
        }
    }

    public function authenticate()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember) || 
            Auth::attempt(['name' => $this->email, 'password' => $this->password], $this->remember)) {
            
            session()->regenerate();

            if (auth()->user()->is_admin) {
                return redirect()->to(route('admin.dashboard'));
            }
            return redirect()->to(route('user.dashboard'));
        }

        $this->addError('email', 'These credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
