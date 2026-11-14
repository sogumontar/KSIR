<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryShowcase;
use App\Models\LaundryShowcasePhoto;
use App\Support\ImageCompressor;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.user')]
#[Title('Before & After - Clean Lab')]
class ShowcaseManager extends Component
{
    use WithFileUploads;

    // Category (treatment) form
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $title = '';
    public string $icon = 'auto_awesome';
    public string $description = '';
    public string $videoUrl = '';
    public bool $isActive = true;

    // Photo pair form (one open at a time)
    public ?int $photoShowcaseId = null;
    public string $photoLabel = '';
    public $beforeUpload;
    public $afterUpload;

    public function openAdd(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $s = $this->ownedShowcase($id);
        $this->editingId   = $s->id;
        $this->title       = $s->title;
        $this->icon        = $s->icon ?: 'auto_awesome';
        $this->description = (string) $s->description;
        $this->videoUrl    = (string) $s->video_url;
        $this->isActive    = $s->is_active;
        $this->showForm    = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:120',
            'icon'        => 'required|string|max:50',
            'description' => 'nullable|string|max:1500',
            'videoUrl'    => 'nullable|url|max:500',
        ]);

        $data = [
            'title'       => $this->title,
            'icon'        => $this->icon,
            'description' => $this->description ?: null,
            'video_url'   => $this->videoUrl ?: null,
            'is_active'   => $this->isActive,
        ];

        if ($this->editingId) {
            $this->ownedShowcase($this->editingId)->update($data);
        } else {
            $data['user_id']    = Auth::id();
            $data['sort_order'] = (int) LaundryShowcase::where('user_id', Auth::id())->max('sort_order') + 1;
            LaundryShowcase::create($data);
        }

        $this->resetForm();
        session()->flash('showcase_msg', 'Kategori treatment disimpan.');
    }

    public function toggleActive(int $id): void
    {
        $s = $this->ownedShowcase($id);
        $s->update(['is_active' => !$s->is_active]);
    }

    public function delete(int $id): void
    {
        $this->ownedShowcase($id)->delete(); // model hook removes the image files too
        session()->flash('showcase_msg', 'Kategori beserta fotonya dihapus.');
    }

    public function openPhotoForm(int $showcaseId): void
    {
        $this->ownedShowcase($showcaseId);
        $this->photoShowcaseId = $showcaseId;
        $this->photoLabel = '';
        $this->beforeUpload = null;
        $this->afterUpload = null;
        $this->resetErrorBag();
    }

    public function closePhotoForm(): void
    {
        $this->photoShowcaseId = null;
        $this->beforeUpload = null;
        $this->afterUpload = null;
    }

    public function savePhoto(): void
    {
        $this->validate([
            'photoLabel'   => 'nullable|string|max:120',
            'beforeUpload' => 'required|image|mimes:jpg,jpeg,png,webp|max:12288',
            'afterUpload'  => 'required|image|mimes:jpg,jpeg,png,webp|max:12288',
        ], [
            'beforeUpload.required' => 'Foto BEFORE wajib diisi.',
            'afterUpload.required'  => 'Foto AFTER wajib diisi.',
        ]);

        $showcase = $this->ownedShowcase((int) $this->photoShowcaseId);

        $before = $after = null;
        try {
            $before = ImageCompressor::store($this->beforeUpload, 'laundry/showcase');
            $after  = ImageCompressor::store($this->afterUpload, 'laundry/showcase');
        } catch (\Throwable $e) {
            if ($before) {
                \Storage::disk('public')->delete($before);
            }
            $this->addError('beforeUpload', $e->getMessage());
            return;
        }

        $showcase->photos()->create([
            'label'       => $this->photoLabel ?: null,
            'before_path' => $before,
            'after_path'  => $after,
            'sort_order'  => (int) $showcase->photos()->max('sort_order') + 1,
        ]);

        $this->closePhotoForm();
        $this->photoLabel = '';
        session()->flash('showcase_msg', 'Foto before/after ditambahkan & dikompres otomatis.');
    }

    public function deletePhoto(int $photoId): void
    {
        $photo = LaundryShowcasePhoto::findOrFail($photoId);
        $this->ownedShowcase($photo->laundry_showcase_id);
        $photo->delete();
    }

    private function ownedShowcase(int $id): LaundryShowcase
    {
        return LaundryShowcase::where('user_id', Auth::id())->findOrFail($id);
    }

    private function resetForm(): void
    {
        $this->reset(['showForm', 'editingId', 'title', 'description', 'videoUrl']);
        $this->icon = 'auto_awesome';
        $this->isActive = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.laundry.showcase-manager', [
            'showcases' => LaundryShowcase::where('user_id', Auth::id())
                ->with('photos')
                ->orderBy('sort_order')->orderBy('id')
                ->get(),
        ]);
    }
}
