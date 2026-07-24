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
    public $price = '';
    public $description = '';
    public $isActive = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string|max:1000',
        'isActive' => 'boolean',
    ];

    public function openAdd()
    {
        $this->reset(['name', 'price', 'description', 'isActive', 'editingId']);
        $this->isActive = true;
        $this->showForm = true;
    }

    public function openEdit($id)
    {
        $service = LaundryService::where('user_id', Auth::id())->findOrFail($id);
        $this->editingId = $service->id;
        $this->name = $service->name;
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

        if ($this->editingId) {
            $service = LaundryService::where('user_id', Auth::id())->findOrFail($this->editingId);
            $service->update([
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'is_active' => $this->isActive,
            ]);
        } else {
            LaundryService::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'is_active' => $this->isActive,
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
