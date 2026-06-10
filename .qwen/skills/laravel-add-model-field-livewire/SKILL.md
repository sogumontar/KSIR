---
name: laravel-add-model-field-livewire
description: Pattern for adding a new database field to a Laravel model and propagating it through migration, model, factory, Livewire component, and blade views with full CRUD support
source: auto-skill
extracted_at: '2026-06-09T11:45:49.714Z'
---

# Adding a New Model Field with Full Livewire UI

This pattern covers the complete chain of changes needed when adding a new field to a Laravel model that uses Livewire components for CRUD UI. Missing any step causes broken forms, missing data, or validation errors.

## Change chain (do all steps, in order)

### 1. Migration
```bash
php artisan make:migration add_{field_name}_to_{table}_table --table={table}
```
- Column type: match the field's purpose (string for enum-like fields, nullable for optional fields)
- Add `->after('column')` to place it logically in the schema
- Include `down()` with `$table->dropColumn('field_name')`
- Run `php artisan migrate` after creating

### 2. Model — fillable + casts
- Add the field to the `#[Fillable([...])]` attribute array
- If the field needs casting (decimal, integer, date), add it in `casts()`
- For snake_case DB columns that map to camelCase Livewire properties: the DB column is `unit_type`, the Livewire property is `unitType` — keep both conventions consistent

### 3. Factory
- Add the field with appropriate fake data generation
- For enum-like fields: define an array of valid values and use `fake()->randomElement($values)`
- Example: `'unit_type' => fake()->randomElement(['pcs', 'box', 'pack', 'set', 'kg', 'liter', 'bundle', 'roll', 'drum', 'unit'])`

### 4. Livewire component — properties, validation, CRUD logic
- Add public properties for add form (`public string $unitType = ''`) and edit form (`public string $editUnitType = ''`)
- Add to `openAdd()` reset array: `$this->reset(['name', 'price', 'stock', 'unitType', 'description'])`
- Add validation rules: `'unitType' => 'required|string|max:50'` (or nullable if optional)
- Add to `saveRecord()` create call: `'unit_type' => $this->unitType` (snake_case for DB, camelCase for property)
- Add to `openEdit()` assignment: `$this->editUnitType = $good->unit_type ?? ''`
- Add to `updateRecord()` validation + update call: `'editUnitType' => 'required|string|max:50'` and `'unit_type' => $this->editUnitType`
- Add to `openView()` viewRecord array: `'unitType' => $good->unit_type ?? '-'`
- If the field is displayed on a **related model's page** (e.g., `unit_type` from Good shown on the Transaction page), fetch the related model in `openView()` and include its field

### 5. Blade view — table column + modal forms
- **Table**: Add a column between relevant existing columns. For enum-like display, use a badge:
  ```html
  <td class="table-cell">
      @if($good->unit_type)
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">{{ ucfirst($good->unit_type) }}</span>
      @else
          <span class="text-slate-400">-</span>
      @endif
  </td>
  ```
- **Add modal**: Add a form field (select dropdown for enum-like, input for free-text):
  ```html
  <select wire:model="unitType" class="form-input bg-white appearance-none w-full">
      <option value="">-- Select Unit Type --</option>
      <option value="pcs">Pieces (pcs)</option>
      <!-- ... more options ... -->
  </select>
  @error('unitType') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
  ```
- **Edit modal**: Same field with `wire:model="editUnitType"` and `@error('editUnitType')`
- **View modal**: Add to the detail grid: `<div class="font-semibold">Unit Type:</div><div>{{ data_get($viewRecord, 'unitType') }}</div>`
- **Related model display**: If showing on a different page (e.g., Good's unit_type on Transaction table), use the relationship:
  ```html
  @if($tx->good && $tx->good->unit_type)
      <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded ...">{{ ucfirst($tx->good->unit_type) }}</span>
  @endif
  ```

### 6. Update colspan in empty-state row
- When adding a column to a table, the `@empty` row's `colspan` must increment: `colspan="5"` → `colspan="6"`

### 7. Good model booted() hook consideration
- If the model has a `static::updated()` hook that cascades field changes to related models (like Good cascading `name`/`price` to Transaction), decide whether the new field should also be cascaded. Only cascade if the related model needs to store a copy of the value independently.

## Making an existing auto-set field editable

When a field like `transaction_date` was previously auto-set (e.g., `now()` on creation) and now needs user input:
- Add Livewire property: `public string $transactionDate = ''`
- Set default in `openAdd()`: `$this->transactionDate = now()->format('Y-m-d')`
- Add date input in add/edit modals with `type="date"` and `wire:model`
- Make validation required: `'transactionDate' => 'required|date'`
- In `saveRecord()`, replace `now()` with `$this->transactionDate`
- In `openEdit()`, format for display: `$this->editTransactionDate = Carbon::parse($tx->transaction_date)->format('Y-m-d')`
- In `updateRecord()`, add `'transaction_date' => $this->editTransactionDate` to the update array
- Mark as mandatory in the UI: `<label class="form-label">Transaction Date <span class="text-error">*</span></label>`

## For file/image upload fields

If the new field is a file upload (image, document, proof of delivery), use the **`laravel-livewire-file-upload`** skill instead — it covers the additional complexity of `WithFileUploads`, file storage, conditional validation, replacement/removal, and preview UI that simple text/enum fields don't need.

## Mobile dropdown sizing fix

For `<select>` elements that appear too small on mobile:
- Add `w-full` to modal form selects so they fill the grid cell width on all screen sizes
- For filter selects in page headers, use `w-full sm:w-auto` and increase padding (`py-2.5`, `min-h-[42px]`) for better tap targets
- Ensure modal grid cells use `grid-cols-1 md:grid-cols-2` so fields stack on mobile