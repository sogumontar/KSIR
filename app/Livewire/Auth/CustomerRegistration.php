<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.guest')]
#[Title('Customer Registration - Inventory Pro')]
class CustomerRegistration extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    public ?string $merchantToken = null;

    public function mount(?string $merchantToken = null)
    {
        $this->merchantToken = $merchantToken;
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone_number',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone,
            'password' => Hash::make($this->password),
            'is_admin' => false,
            'status' => 'active',
        ]);

        if ($this->merchantToken) {
            // Bind merchant to customer
            $merchant = User::where('unique_code', $this->merchantToken)->first();
            if ($merchant) {
                $user->merchants()->attach($merchant->id);
            }
        }

        Auth::login($user);

        return redirect()->route('customer.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.customer-registration');
    }
}
