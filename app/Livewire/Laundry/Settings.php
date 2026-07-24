<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryMerchantSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.user')]
#[Title('Laundry Settings - Inventory Pro')]
class Settings extends Component
{
    use WithFileUploads;

    public $qrCode;
    public $paymentNotes = '';
    public $existingQrPath = null;

    public function mount()
    {
        $setting = LaundryMerchantSetting::firstOrCreate([
            'user_id' => Auth::id()
        ], [
            'payment_notes' => ''
        ]);

        $this->paymentNotes = $setting->payment_notes;
        $this->existingQrPath = $setting->qr_code_path;
    }

    public function save()
    {
        $this->validate([
            'qrCode' => 'nullable|image|max:2048', // Max 2MB
            'paymentNotes' => 'nullable|string|max:1000',
        ]);

        $setting = LaundryMerchantSetting::where('user_id', Auth::id())->first();

        if ($this->qrCode) {
            if ($setting->qr_code_path) {
                Storage::disk('public')->delete($setting->qr_code_path);
            }
            $path = $this->qrCode->store('laundry/qr', 'public');
            $setting->qr_code_path = $path;
            $this->existingQrPath = $path;
        }

        $setting->payment_notes = $this->paymentNotes;
        $setting->save();

        session()->flash('message', 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.laundry.settings');
    }
}
