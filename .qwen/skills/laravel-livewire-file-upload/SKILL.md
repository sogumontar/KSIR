---
name: laravel-livewire-file-upload
description: Pattern for adding file/image upload fields to a Livewire CRUD component with storage, conditional validation, replacement/removal, and preview UI
source: auto-skill
extracted_at: '2026-06-09T12:38:32.815Z'
---

# Adding File Upload Fields to Livewire CRUD Components

This pattern covers adding file upload capabilities (images, documents) to a Livewire component that already has Add/Edit/View modals. It handles storage (file on disk, path in DB), conditional required validation, file replacement/removal, and preview UI in blade templates.

## Key differences from simple field additions

File uploads require the `WithFileUploads` trait, temporary file handling, conditional validation (e.g., required only when certain statuses are set), file deletion on replacement/removal, and distinct UI patterns (hidden file input triggered by click, upload previews with filename/size).

## Change chain (do all steps)

### 1. Migration — nullable string column for the path
```bash
php artisan make:migration add_{field_name}_to_{table}_table
```
- Use `nullable string` column — the DB stores the relative storage path, not the file itself
- Example: `$table->string('proof_of_delivery')->nullable()->after('due_date')`
- Or: `$table->string('image')->nullable()->after('unit_type')`
- Run `php artisan migrate` after creating

### 2. Model — add to fillable
- Add the field to `#[Fillable([...])]`
- No special cast needed — it's just a string path

### 3. Livewire component — WithFileUploads trait + properties + CRUD logic

**Add trait:**
```php
use Livewire\WithFileUploads;
// In class body:
use WithPagination, WithFileUploads;
```

**Add properties (use `$file` naming, not the DB column name):**
```php
// For add form
public $proofFile;       // no type declaration — Livewire resolves this as TemporaryUploadedFile
public $imageFile;

// For edit form — new upload file
public $editProofFile;
public $editImageFile;

// For edit form — track whether an existing file is present (so UI knows to show "replace" vs "upload")
public ?string $existingProof = null;
public ?string $existingImage = null;
```

**Add form (`openAdd` reset + `saveRecord`):**
- Add the file property to the `reset()` array in `openAdd()`
- Conditional validation (required when certain conditions, optional otherwise):
  ```php
  $requiresProof = in_array($this->status, ['delivered', 'loan']);
  $rules['proofFile'] = $requiresProof
      ? 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
      : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';
  ```
- For images (always optional): `'imageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'`
- Add explicit error before validate for conditional required:
  ```php
  if ($requiresProof && !$this->proofFile) {
      $this->addError('proofFile', 'Proof of delivery is required when status is delivered or loan.');
  }
  ```
- Store file and get path:
  ```php
  $proofPath = null;
  if ($this->proofFile) {
      $proofPath = $this->proofFile->store('proofs', 'public');
  }
  ```
- Include in create: `'proof_of_delivery' => $proofPath`

**Edit form (`openEdit` + `updateRecord`):**
- Load existing file path in `openEdit()`:
  ```php
  $this->existingProof = $tx->proof_of_delivery;
  $this->editProofFile = null;  // always reset new upload
  ```
- Conditional validation for edit — required only when status demands it AND no existing file:
  ```php
  $rules['editProofFile'] = $requiresProof && !$this->existingProof
      ? 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
      : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';
  ```
- Handle replacement/removal in `updateRecord()`:
  ```php
  $proofPath = $transaction->proof_of_delivery;
  if ($this->editProofFile) {
      // New file uploaded — delete old, store new
      if ($proofPath) {
          \Storage::disk('public')->delete($proofPath);
      }
      $proofPath = $this->editProofFile->store('proofs', 'public');
  } elseif (!$this->existingProof && $proofPath) {
      // User removed existing file via "remove" button — delete from disk, set null in DB
      \Storage::disk('public')->delete($proofPath);
      $proofPath = null;
  }
  // else: no change, keep existing path
  ```
- Include in update: `'proof_of_delivery' => $proofPath`

**View (`openView`):**
- Pass the actual DB path (not hardcoded string):
  ```php
  'proof' => $tx->proof_of_delivery,
  'image' => $good->image,
  ```

### 4. Blade — upload UI patterns

**Hidden file input triggered by container click (both Add and Edit):**
```html
<div x-show="!$wire.proofFile" class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors cursor-pointer"
     onclick="this.querySelector('input[type=file]').click()">
    <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
    <p class="font-label-md text-slate-700 mb-1">Click to upload or drag and drop</p>
    <p class="text-sm text-slate-500">PDF, JPG, or PNG (max. 10MB)</p>
    <input accept=".pdf,.jpg,.jpeg,.png" class="hidden" type="file" wire:model="proofFile">
</div>
```

**Upload preview (shows after file is selected, with remove button):**
```html
@if($proofFile)
    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
        <span class="material-symbols-outlined text-green-600">check_circle</span>
        <span class="text-sm text-green-800 font-medium">{{ $proofFile->getClientOriginalName() }}</span>
        <span class="text-xs text-green-600">{{ number_format($proofFile->getSize() / 1024, 1) }} KB</span>
        <button wire:click="$set('proofFile', null)" class="text-red-500 hover:text-red-700 ml-auto">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
@endif
```

**Image upload preview (uses `temporaryUrl()` for live thumbnail):**
```html
@if($imageFile)
    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
        <img src="{{ $imageFile->temporaryUrl() }}" alt="Preview" class="w-16 h-16 rounded object-cover border border-green-300">
        <div class="flex-1">
            <span class="text-sm text-green-800 font-medium">{{ $imageFile->getClientOriginalName() }}</span>
            <span class="text-xs text-green-600 ml-2">{{ number_format($imageFile->getSize() / 1024, 1) }} KB</span>
        </div>
        <button wire:click="$set('imageFile', null)" class="text-red-500 hover:text-red-700">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
@endif
```

**Edit modal — existing file display with "View" link + "Remove" button:**
```html
@if($existingProof)
    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-3">
        <span class="material-symbols-outlined text-blue-600">description</span>
        <span class="text-sm text-blue-800 font-medium">Current proof uploaded</span>
        <a href="{{ asset('storage/' . $existingProof) }}" target="_blank" class="text-sm text-blue-600 underline ml-2">View file</a>
        <button wire:click="$set('existingProof', null)" class="text-red-500 hover:text-red-700 ml-auto" title="Remove existing proof">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
@endif
```
- For images, replace the document icon with `<img src="{{ asset('storage/' . $existingImage) }}" class="w-16 h-16 rounded object-cover">`

**Conditional required indicator in label:**
```html
<label class="form-label">
    Proof of Delivery / Manifest
    <span x-show="$wire.status === 'delivered' || $wire.status === 'loan'" class="text-error">*</span>
</label>
```
- For edit modal, also check `!$wire.existingProof`: `<span x-show="$wire.editStatus === 'delivered' || $wire.editStatus === 'loan' && !$wire.existingProof" class="text-error">*</span>`

**View modal — file link/download:**
```html
@if(data_get($viewRecord, 'proof'))
    <a href="{{ asset('storage/' . data_get($viewRecord, 'proof')) }}" target="_blank"
       class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors">
        <span class="material-symbols-outlined text-sm">description</span>
        <span class="text-sm font-medium">View / Download</span>
    </a>
@else
    <span class="text-slate-400 text-sm">No proof uploaded</span>
@endif
```
- For images in View modal: `<img src="{{ asset('storage/' . data_get($viewRecord, 'image')) }}" class="w-32 h-32 rounded-lg object-cover border border-slate-200 shadow-sm">`

**Table thumbnail for images:**
```html
<td class="table-cell">
    @if($good->image)
        <img src="{{ asset('storage/' . $good->image) }}" alt="{{ $good->name }}" class="w-10 h-10 rounded object-cover border border-slate-200">
    @else
        <div class="w-10 h-10 rounded bg-slate-100 border border-slate-200 flex items-center justify-center">
            <span class="material-symbols-outlined text-slate-400 text-lg">image</span>
        </div>
    @endif
</td>
```

### 5. Storage setup
- Run `php artisan storage:link` to create the public symlink
- Files stored via `$file->store('directory', 'public')` go to `storage/app/public/{directory}/`
- Accessible via web at `/storage/{directory}/filename.ext`
- Use `asset('storage/' . $path)` to generate URLs in blade

### 6. Validation rules reference

| Context | Rule pattern |
|---------|-------------|
| Document (always optional) | `nullable|file|mimes:pdf,jpg,jpeg,png|max:10240` |
| Document (conditional required) | `required|file|mimes:...` when condition met, `nullable|file|mimes:...` otherwise |
| Image (always optional) | `nullable|image|mimes:jpg,jpeg,png,webp|max:2048` |
| Conditional required in edit | `required` only when condition met AND `$existingFile` is null |

### 7. File lifecycle management
- **Create**: store new file, save path to DB
- **Update with new file**: delete old file from disk → store new → update DB path
- **Update removing existing**: delete file from disk → set DB path to null
- **Update no change**: keep existing path unchanged
- **Delete record**: consider whether to also delete the file from disk (depends on whether other records reference the same file — usually safe to delete for per-record attachments)