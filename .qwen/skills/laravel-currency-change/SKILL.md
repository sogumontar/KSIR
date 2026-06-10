---
name: laravel-currency-change
description: Pattern for changing currency display across a Laravel+Livewire project — PHP number_format, JS Intl.NumberFormat, Chart.js callbacks, form labels, and PDF exports
source: auto-skill
extracted_at: '2026-06-09T14:08:12.963Z'
---

# Changing Currency Display Across a Laravel + Livewire Project

When switching currencies (e.g., Dollar → Rupiah), every display point must be updated consistently. This project uses currency in 3 layers: PHP server-side, JavaScript client-side, and Chart.js charts. Missing any layer causes inconsistent UX.

## Display layers to update

### Layer 1: PHP — `$` prefix + `number_format()` in blade templates

Replace all `${{ number_format($value, 2) }}` patterns with `Rp{{ number_format($value, 0) }}`:
- Change prefix: `$` → `Rp`
- Change decimal places: For Rupiah (IDR), use `0` decimal places since it has no subdivision. For currencies with cents (USD, EUR), keep `2`.
- Example: `${{ number_format($tx->total_price, 2) }}` → `Rp{{ number_format($tx->total_price, 0) }}`

Also update Livewire component properties that format currency on the PHP side:
```php
// Before
$this->totalSales = '$' . number_format($total, 0);
// After  
$this->totalSales = 'Rp' . number_format($total, 0);
```

And the default value:
```php
// Before
public string $totalSales = '$0';
// After
public string $totalSales = 'Rp0';
```

### Layer 2: JS — `Intl.NumberFormat` in Alpine.js expressions

Replace `Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' })` with the target locale/currency:

For Indonesian Rupiah:
```js
// Before
new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
// After
new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value)
```

Key differences:
- Locale: `'en-US'` → `'id-ID'`
- Currency code: `'USD'` → `'IDR'`
- `minimumFractionDigits: 0` for IDR (no decimals) — this is critical because `Intl.NumberFormat` with `style: 'currency'` defaults to 2 decimal places, but IDR conventionally has no decimals

Also update the fallback text in the same element:
```html
<!-- Before -->
x-text="new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(addTotal)">$0.00</span>
<!-- After -->
x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(addTotal)">Rp0</span>
```

### Layer 3: Chart.js — tooltip callbacks + y-axis tick labels

**Tooltip label callback:**
```js
// Before
return '$' + context.parsed.y.toLocaleString();
// After (for IDR)
return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
```

**Y-axis tick callback:**
```js
// Before
callback: function(value) { return '$' + value.toLocaleString(); }
// After (for IDR)
callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
```

**Dataset label:**
```js
// Before
label: 'Revenue ($)',
// After
label: 'Revenue (Rp)',
```

### Layer 4: Form labels

Update any labels that reference the currency symbol:
```html
<!-- Before -->
<label class="form-label">Unit Price ($)</label>
<label class="form-label">Price ($)</label>
<!-- After -->
<label class="form-label">Unit Price (Rp)</label>
<label class="form-label">Price (Rp)</label>
```

Use `replace_all: true` when editing blade files for label changes since the same label pattern appears in both Add and Edit modals.

### Layer 5: PDF exports

PDF templates often have separate currency formatting. Update all price displays:
```html
<!-- Before -->
<td class="text-right">${{ number_format($tx->price ?? 0, 2) }}</td>
<td class="text-right">${{ number_format($tx->total_price ?? 0, 2) }}</td>
<strong>Grand Total: ${{ number_format($transactions->sum('total_price'), 2) }}</strong>
<!-- After (IDR) -->
<td class="text-right">Rp{{ number_format($tx->price ?? 0, 0) }}</td>
<td class="text-right">Rp{{ number_format($tx->total_price ?? 0, 0) }}</td>
<strong>Grand Total: Rp{{ number_format($transactions->sum('total_price'), 0) }}</strong>
```

## Files to check for currency references

When changing currency, grep these patterns across the entire views directory:

1. `\$\{\{ number_format` — PHP dollar prefix + number_format (this catches `${{ ... }}`)
2. `'USD'` or `"USD"` — JS Intl.NumberFormat currency code
3. `"en-US"` or `'en-US'` — JS locale strings
4. `'\$'` — Chart.js dollar prefix in callback strings
5. `Price (\$)` or `Unit Price (\$)` — form labels

Do NOT change `number_format` calls that are NOT currency (e.g., file sizes in KB like `number_format($file->getSize() / 1024, 1)` or counts like `number_format($totalUsers)`).

## IDR formatting conventions

- No decimal places: `Rp1.500.000` not `Rp1.500.000,00`
- Use `number_format($value, 0)` in PHP (0 decimal places)
- Use `minimumFractionDigits: 0` in `Intl.NumberFormat`
- In Chart.js, use `toLocaleString('id-ID')` which produces Indonesian-style number grouping (e.g., `1.500.000`)
- The `Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' })` formatter produces `Rp1.500.000` automatically when `minimumFractionDigits: 0` is set