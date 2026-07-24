<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryPromo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.user')]
#[Title('Promo Manager - Laundry - Inventory Pro')]
class PromoManager extends Component
{
    public $search = '';
    public $showForm = false;
    public $editingId = null;

    public $name = '';
    public $type = 'percentage';
    public $percent = null;
    public $buyQty = null;
    public $freeQty = null;
    public $isActive = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,accumulative',
            'percent' => 'required_if:type,percentage|nullable|numeric|min:1|max:100',
            'buyQty' => 'required_if:type,accumulative|nullable|integer|min:1',
            'freeQty' => 'required_if:type,accumulative|nullable|integer|min:1',
            'isActive' => 'boolean',
        ];
    }

    public function updatedType()
    {
        if ($this->type === 'percentage') {
            $this->buyQty = null;
            $this->freeQty = null;
        } else {
            $this->percent = null;
        }
    }

    public function openAdd()
    {
        $this->reset(['name', 'type', 'percent', 'buyQty', 'freeQty', 'isActive', 'editingId']);
        $this->isActive = true;
        $this->type = 'percentage';
        $this->showForm = true;
    }

    public function openEdit($id)
    {
        $promo = LaundryPromo::where('user_id', Auth::id())->findOrFail($id);
        $this->editingId = $promo->id;
        $this->name = $promo->name;
        $this->type = $promo->type;
        $this->percent = $promo->discount_percent;
        $this->buyQty = $promo->buy_quantity;
        $this->freeQty = $promo->free_quantity;
        $this->isActive = $promo->is_active;
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
            $promo = LaundryPromo::where('user_id', Auth::id())->findOrFail($this->editingId);
            $promo->update([
                'name' => $this->name,
                'type' => $this->type,
                'discount_percent' => $this->percent,
                'buy_quantity' => $this->buyQty,
                'free_quantity' => $this->freeQty,
                'is_active' => $this->isActive,
            ]);
        } else {
            LaundryPromo::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'type' => $this->type,
                'discount_percent' => $this->percent,
                'buy_quantity' => $this->buyQty,
                'free_quantity' => $this->freeQty,
                'is_active' => $this->isActive,
            ]);
        }

        $this->showForm = false;
    }

    public function delete($id)
    {
        LaundryPromo::where('user_id', Auth::id())->findOrFail($id)->delete();
    }

    public function render()
    {
        $promos = LaundryPromo::where('user_id', Auth::id())
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.laundry.promo-manager', [
            'promos' => $promos,
        ]);
    }
}
