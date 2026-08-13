<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.user')]
#[Title('Service Manager - Laundry - Inventory Pro')]
class ServiceManager extends Component
{
    public $search = '';
    public $showForm = false;
    public $editingId = null;

    public $name = '';
    public $shortCode = '';
    public $price = '';
    public $description = '';
    public $isActive = true;

    protected $rules = [
        'name'        => 'required|string|max:255',
        'shortCode'   => 'nullable|string|max:20|regex:/^[A-Za-z0-9_-]*$/',
        'price'       => 'required|numeric|min:0',
        'description' => 'nullable|string|max:1000',
        'isActive'    => 'boolean',
    ];

    protected $messages = [
        'shortCode.regex' => 'Short code may only contain letters, numbers, hyphens, and underscores.',
    ];

    public function openAdd()
    {
        $this->reset(['name', 'shortCode', 'price', 'description', 'isActive', 'editingId']);
        $this->isActive = true;
        $this->showForm = true;
    }

    public function openEdit($id)
    {
        $service = LaundryService::where('user_id', Auth::id())->findOrFail($id);
        $this->editingId = $service->id;
        $this->name = $service->name;
        $this->shortCode = $service->short_code ?? '';
        $this->price = $service->price;
        $this->description = $service->description;
        $this->isActive = $service->is_active;
        $this->showForm = true;
    }

    public function cancel()
    {
        $this->showForm = false;
    }

    public function save()
    {
        $this->validate();
        $code = strtoupper(trim($this->shortCode)) ?: null;

        if ($this->editingId) {
            $service = LaundryService::where('user_id', Auth::id())->findOrFail($this->editingId);
            $service->update([
                'name'        => $this->name,
                'short_code'  => $code,
                'price'       => $this->price,
                'description' => $this->description,
                'is_active'   => $this->isActive,
            ]);
        } else {
            LaundryService::create([
                'user_id'     => Auth::id(),
                'name'        => $this->name,
                'short_code'  => $code,
                'price'       => $this->price,
                'description' => $this->description,
                'is_active'   => $this->isActive,
            ]);
        }

        $this->showForm = false;
    }

    public function delete($id)
    {
        LaundryService::where('user_id', Auth::id())->findOrFail($id)->delete();
    }

    public function render()
    {
        $services = LaundryService::where('user_id', Auth::id())
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->get();

        return view('livewire.laundry.service-manager', [
            'services' => $services,
        ]);
    }
}
