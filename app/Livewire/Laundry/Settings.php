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
    public $headerBgImage;
    public $paymentNotes    = '';
    public $storeName       = '';
    public $storeAddress    = '';
    public $existingQrPath  = null;
    public $existingBgPath  = null;

    public function mount()
    {
        $setting = LaundryMerchantSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            ['payment_notes' => '']
        );

        $this->paymentNotes   = $setting->payment_notes  ?? '';
        $this->storeName      = $setting->store_name     ?? '';
        $this->storeAddress   = $setting->store_address  ?? '';
        $this->existingQrPath = $setting->qr_code_path;
        $this->existingBgPath = $setting->header_bg_image;
    }

    public function save()
    {
        $this->validate([
            'qrCode'          => 'nullable|image|max:2048',
            'headerBgImage'   => 'nullable|image|max:5120',
            'paymentNotes'    => 'nullable|string|max:1000',
            'storeName'       => 'nullable|string|max:255',
            'storeAddress'    => 'nullable|string|max:500',
        ]);

        $setting = LaundryMerchantSetting::where('user_id', Auth::id())->first();

        if ($this->qrCode) {
            if ($setting->qr_code_path) {
                Storage::disk('public')->delete($setting->qr_code_path);
            }
            $path = $this->qrCode->store('laundry/qr', 'public');
            $setting->qr_code_path = $path;
            $this->existingQrPath  = $path;
        }

        if ($this->headerBgImage) {
            if ($setting->header_bg_image) {
                Storage::disk('public')->delete($setting->header_bg_image);
            }
            $bgPath = $this->headerBgImage->store('laundry/bg', 'public');
            $setting->header_bg_image = $bgPath;
            $this->existingBgPath     = $bgPath;
        }

        $setting->payment_notes = $this->paymentNotes;
        $setting->store_name    = $this->storeName;
        $setting->store_address = $this->storeAddress;
        $setting->save();

        session()->flash('message', 'Settings saved successfully.');
    }

    public function removeBg()
    {
        $setting = LaundryMerchantSetting::where('user_id', Auth::id())->first();
        if ($setting?->header_bg_image) {
            Storage::disk('public')->delete($setting->header_bg_image);
            $setting->header_bg_image = null;
            $setting->save();
            $this->existingBgPath = null;
        }
    }

    public function render()
    {
        return view('livewire.laundry.settings');
    }
}
