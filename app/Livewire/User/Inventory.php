<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Good;

#[Layout('components.layouts.user')]
#[Title('Goods Inventory - Inventory Pro')]
class Inventory extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showAddModal = false;
    public bool $showEditModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $recordToDelete = null;

    // Add form properties
    public string $name = '';
    public float $price = 0.0;
    public int $stock = 0;
    public string $unitType = '';
    public string $description = '';
    public $imageFile;

    // Edit form properties
    public ?int $editGoodId = null;
    public string $editName = '';
    public float $editPrice = 0.0;
    public int $editStock = 0;
    public string $editUnitType = '';
    public string $editDescription = '';
    public $editImageFile;
    public ?string $existingImage = null;

    // View record details
    public array $viewRecord = [];

    public string $search = '';

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAdd()
    {
        $this->reset(['name', 'price', 'stock', 'unitType', 'description', 'imageFile']);
        $this->showAddModal = true;
    }

    public function saveRecord()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unitType' => 'required|string|max:50',
            'description' => 'nullable|string',
            'imageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($this->imageFile) {
            $imagePath = $this->imageFile->store('goods', 'public');
        }

        Good::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'unit_type' => $this->unitType,
            'description' => $this->description,
            'image' => $imagePath,
        ]);

        $this->showAddModal = false;
        $this->reset(['name', 'price', 'stock', 'unitType', 'description', 'imageFile']);
        session()->flash('message', 'Good created successfully.');
    }

    public function openEdit(int $id)
    {
        $good = Good::where('user_id', auth()->id())->findOrFail($id);
        $this->editGoodId = $id;
        $this->editName = $good->name;
        $this->editPrice = (float) $good->price;
        $this->editStock = (int) $good->stock;
        $this->editUnitType = $good->unit_type ?? '';
        $this->editDescription = $good->description ?? '';
        $this->existingImage = $good->image;
        $this->editImageFile = null;
        $this->showEditModal = true;
    }

    public function updateRecord()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editPrice' => 'required|numeric|min:0',
            'editStock' => 'required|integer|min:0',
            'editUnitType' => 'required|string|max:50',
            'editDescription' => 'nullable|string',
            'editImageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($this->editGoodId) {
            $good = Good::where('user_id', auth()->id())->findOrFail($this->editGoodId);

            $imagePath = $good->image;
            if ($this->editImageFile) {
                if ($imagePath) {
                    \Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $this->editImageFile->store('goods', 'public');
            } elseif (!$this->existingImage && $imagePath) {
                \Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            $good->update([
                'name' => $this->editName,
                'price' => $this->editPrice,
                'stock' => $this->editStock,
                'unit_type' => $this->editUnitType,
                'description' => $this->editDescription,
                'image' => $imagePath,
            ]);
        }

        $this->showEditModal = false;
        session()->flash('message', 'Good updated successfully.');
    }

    public function openView(int $id)
    {
        $good = Good::where('user_id', auth()->id())->findOrFail($id);
        $this->viewRecord = [
            'name' => $good->name,
            'price' => (float) $good->price,
            'stock' => (int) $good->stock,
            'unitType' => $good->unit_type ?? '-',
            'description' => $good->description ?? '-',
            'image' => $good->image,
            'created' => $good->created_at->format('M d, Y H:i'),
        ];
        $this->showViewModal = true;
    }

    public function confirmDelete(int $id)
    {
        $this->recordToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRecord()
    {
        if ($this->recordToDelete) {
            Good::where('user_id', auth()->id())
                ->where('id', $this->recordToDelete)
                ->delete();
        }
        $this->showDeleteModal = false;
        $this->recordToDelete = null;
        session()->flash('message', 'Good deleted successfully.');
    }

    public function mount()
    {
    }

    public function render()
    {
        $goods = Good::where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.user.inventory', [
            'goods' => $goods,
        ]);
    }
}