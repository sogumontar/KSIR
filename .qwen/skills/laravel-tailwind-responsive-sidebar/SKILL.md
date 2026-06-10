---
name: laravel-tailwind-responsive-sidebar
description: Pattern for converting a fixed desktop sidebar into a mobile-responsive off-canvas menu using Alpine.js in Laravel + Livewire layouts
source: auto-skill
extracted_at: '2026-06-09T05:30:55.364Z'
---

# Responsive Sidebar Pattern for Laravel + Livewire

This pattern converts a fixed-width sidebar (e.g., 280px) into a mobile-responsive off-canvas overlay menu, using Alpine.js directives that come bundled with Livewire v4.

## Key decisions from this project
- **Overlay approach** was chosen over push (sidebar slides over content, with backdrop)
- **Breakpoint**: `lg:` (1024px+) shows the sidebar permanently; below that it's hidden by default
- Livewire v4 includes Alpine.js via `@livewireScripts` — no separate npm install needed

## Implementation steps

### 1. Add Alpine state to `<body>`
```html
<body x-data="{ sidebarOpen: false }">
```

### 2. Add overlay backdrop (mobile only)
```html
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-primary/50 backdrop-blur-sm z-40 lg:hidden"
    x-cloak
></div>
```

### 3. Sidebar: hidden by default on mobile, visible on lg+
```html
<aside
    class="fixed left-0 top-0 h-full w-[280px] ... z-50
           transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
    :class="sidebarOpen && 'translate-x-0'"
>
```
- `-translate-x-full` hides it on mobile
- `lg:translate-x-0` always shows on desktop
- `:class="sidebarOpen && 'translate-x-0'"` slides in when toggled on mobile
- Nav links get `@click="sidebarOpen = false"` to dismiss after navigation

### 4. Hamburger button in header (mobile only)
```html
<button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 rounded-lg text-on-surface-variant hover:bg-surface-container-low" x-cloak>
    <span class="material-symbols-outlined" data-icon="menu">menu</span>
</button>
```

### 5. Main content: offset only on desktop
```html
<div class="lg:ml-[280px] flex flex-col min-h-screen">
```
Remove any hardcoded `ml-[280px]` — use `lg:ml-[280px]` so content is full-width on mobile.

### 6. CSS requirement
Add `[x-cloak] { display: none !important; }` to `resources/css/app.css` after `@tailwind utilities` so Alpine-hidden elements don't flash on page load.

## Container queries for dashboard KPI grids
Use **both** viewport breakpoints and container queries together — container queries alone can fail if the container element's width never reaches the `@md` threshold (e.g., inside a sidebar-offset layout where available width is reduced):
```html
<section class="@container grid grid-cols-1 md:grid-cols-3 @sm:grid-cols-2 @md:grid-cols-3 gap-gutter">
```
- `md:grid-cols-3` (viewport) ensures cards are parallel on desktop regardless of container size
- `@sm:grid-cols-2 @md:grid-cols-3` (container) provide finer control within nested contexts
- This requires `@tailwindcss/container-queries` plugin in `tailwind.config.js` plugins array

## Responsive tables
- Wrap `<table>` in `<div class="overflow-x-auto">`
- Add `min-w-[500px]` (or appropriate width) to `<table>` so columns don't collapse
- Filter bars: `flex-col lg:flex-row` with `items-stretch lg:items-end`
- Pagination: `flex-col sm:flex-row` so text and links stack on small screens

## Responsive bulk action bars (with export/action buttons)
On mobile, inline action bars with `ml-auto` can push buttons off-screen. Use `flex-wrap` with responsive sizing:
```html
<div class="flex flex-wrap items-center gap-2 sm:gap-4">
    <!-- select/clear buttons stay inline -->
    <div class="sm:ml-auto w-full sm:w-auto">
        <button class="w-full sm:w-auto justify-center ...">Export Selected</button>
    </div>
</div>
```
- `ml-auto` becomes `sm:ml-auto` so it only pushes right on desktop
- Export wrapper: `w-full sm:w-auto` so it takes a full row on mobile, inline on desktop
- Button: `w-full sm:w-auto justify-center` for full-width mobile, inline desktop

## Select-all via header checkbox (instead of separate buttons)
Replace "Select All" and "Clear Selection" buttons with a single header checkbox that toggles. Use Alpine `@change` to call Livewire methods conditionally:
```html
<th class="table-header w-12">
    <input type="checkbox"
           @change="$el.checked ? $wire.selectAll() : $wire.clearSelection()"
           class="form-checkbox rounded" />
</th>
```
- Checked → `$wire.selectAll()`, unchecked → `$wire.clearSelection()`
- The Livewire component must have `selectAll()` and `clearSelection()` public methods
- Row checkboxes use `wire:model.live="selected"` with `value="{{ $tx->id }}"`

## Navbar full-width fix with sidebar layouts
When a sidebar layout has a `lg:ml-[280px]` offset, wrap **both** the `<header>` and `<main>` inside a single shared offset div — do NOT offset them separately:
```html
<aside class="fixed left-0 top-0 h-full w-[280px] ...">...</aside>

<!-- Main Content Area -->
<div class="lg:ml-[280px] flex flex-col min-h-screen transition-all duration-300">
    <header class="sticky top-0 h-16 flex justify-between items-center ...">
        <!-- hamburger, search, profile -->
    </header>
    <main class="p-md lg:p-lg max-w-[1440px] mx-auto w-full space-y-lg">
        {{ $slot }}
    </main>
</div>
```
- The header should NOT have its own `lg:w-[calc(100%-280px)]` or `lg:ml-[280px]` — the parent wrapper handles the offset
- This pattern matches the admin layout structure and ensures the navbar spans the full available width

## Header right-side items alignment (ml-auto pattern)
When a header bar has a hamburger button on the left (hidden on desktop via `lg:hidden`) and action items (notification, logout) on the right, the action items **must** have `ml-auto` to stay right-aligned on desktop. Without it, when the hamburger is removed from the flex flow on desktop (`lg:hidden` → `display:none`), `justify-between` with only one visible child places that child at the **start** (left) of the flex container:

```html
<!-- BROKEN: on desktop, hamburger is lg:hidden, so the only visible child (the right-side div)
     gets placed at flex-start by justify-between -->
<header class="sticky top-0 h-16 flex justify-between items-center ...">
    <button class="lg:hidden ...">hamburger</button>
    <div class="flex items-center gap-lg">
        <!-- notification, logout — appear on LEFT on desktop! -->
    </div>
</header>

<!-- FIXED: ml-auto always pushes the right-side div to the end regardless of other children -->
<header class="sticky top-0 h-16 flex justify-between items-center ...">
    <button class="lg:hidden ...">hamburger</button>
    <div class="ml-auto flex items-center gap-lg">
        <!-- notification, logout — appear on RIGHT on desktop -->
    </div>
</header>
```
- `ml-auto` works on both mobile (with hamburger visible, items go right) and desktop (hamburger hidden, items still go right)
- `justify-between` is still useful for the mobile layout but should NOT be relied upon to position right-side items on desktop
- This is a common bug in sidebar layouts where a left-side hamburger disappears at the `lg:` breakpoint

## Page header stacking (title + action button)
On mobile, `flex justify-between` squishes title and action buttons. Stack them vertically:
```html
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-md mb-xl">
    <div><!-- title --></div>
    <div class="relative"><!-- action buttons / dropdowns --></div>
</div>
```
- `flex-col` on mobile: title above buttons
- `sm:flex-row sm:justify-between`: side-by-side on desktop

## Date range inputs (avoid shared border box)
Do NOT put two `<input type="date">` inside a single bordered container with a "to" separator — the native calendar picker icon will overflow/overlap the separator area. Instead, use separate bordered inputs:
```html
<div class="flex flex-col sm:flex-row sm:items-center gap-xs">
    <input wire:model="dateFrom" class="border border-outline-variant rounded-lg bg-surface-container-lowest p-2 w-full" type="date"/>
    <span class="text-outline text-center sm:px-xs">to</span>
    <input wire:model="dateTo" class="border border-outline-variant rounded-lg bg-surface-container-lowest p-2 w-full" type="date"/>
</div>
```
- Stacked on mobile (`flex-col`), side-by-side on desktop (`sm:flex-row`)
- Each input has its own border, preventing calendar icon overlap

## Chart/section header stacking
For sections with a title + filter/control buttons side-by-side, stack on mobile:
```html
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-md mb-lg">
    <h4>Title</h4>
    <div><!-- filter buttons / segmented controls --></div>
</div>
```

## Filter button overflow on mobile (segmented controls)
When a segmented control (Daily/Weekly/Monthly/Annual) sits inside a card, it can overflow the card boundary on mobile. Fix with `overflow-x-auto` on the wrapper and `flex-shrink-0` on the buttons container:
```html
<div class="flex items-center gap-md overflow-x-auto">
    <div class="flex bg-surface-container-low p-base rounded-lg border border-outline-variant flex-shrink-0">
        <!-- buttons -->
    </div>
</div>
```
- `overflow-x-auto` allows horizontal scroll when buttons are too wide
- `flex-shrink-0` prevents the buttons container from being squeezed below its min-content size

## Custom CSS overriding Tailwind utilities (common pitfall)
Custom CSS classes in `app.css` can override Tailwind utility classes if they set the same property with equal or higher specificity. For example, `.table-header { text-align: left; }` overrides `text-right`/`text-center` on `<th>` elements because the custom CSS has the same specificity as a utility class but the declaration order matters.

**Fix:** Remove the conflicting property from the custom CSS and let Tailwind utilities handle it. Default to `text-align: left` via Tailwind's default on `<th>` (or add it explicitly as a class), and use `text-right`/`text-center` only where needed:
```css
/* BEFORE (broken — overrides Tailwind text-right/text-center) */
.table-header {
    background-color: #0f172a;
    color: white;
    font-weight: 600;
    padding: 16px;
    text-align: left;  /* ← REMOVE THIS */
}

/* AFTER (Tailwind utilities control alignment) */
.table-header {
    background-color: #0f172a;
    color: white;
    font-weight: 600;
    padding: 16px;
}
```

## Critical pitfall: editing Alpine.js x-data on layout wrapper elements

When modifying the `x-data` attribute on the `<body>` element (or any parent element that wraps the sidebar overlay `<div>` and `<aside>` tag), be extremely careful about edit boundaries. A single edit that replaces the `x-data` string can accidentally swallow the surrounding structural HTML — the Mobile Sidebar Overlay `<div>` and the `<aside>` opening tag with its critical positioning classes (`fixed left-0 top-0 h-full w-[280px] ... z-50`). This happened in this project: an edit to change `x-data="{ sidebarOpen: false }"` to `x-data="{ sidebarOpen: false, notifOpen: false }"` accidentally deleted the overlay div and aside opening tag, leaving only an orphaned `:class="sidebarOpen && 'translate-x-0'"` line and the sidebar content without its wrapper.

**How to avoid:** When editing `x-data` on `<body>`, only target the exact `x-data="{ ... }"` attribute string itself. Do NOT include the closing `>` of the `<body>` tag or any subsequent lines in your `old_string`. After the edit, always re-read the file to verify the overlay `<div>` and `<aside>` tag are intact. If they were accidentally removed, restore them immediately — the sidebar will be completely broken (no positioning, no background, no z-index, no responsive behavior).

**Recovery pattern:** If the overlay and aside are lost, the file will look like:
```html
<body ... x-data="{ sidebarOpen: false, notifOpen: false }">
    :class="sidebarOpen && 'translate-x-0'"
>
    <div class="p-lg">
```
Replace the broken lines with the full overlay + aside restoration:
```html
<body ... x-data="{ sidebarOpen: false, notifOpen: false }">

<!-- Mobile Sidebar Overlay -->
<div x-show="sidebarOpen" ... transitions ... @click="sidebarOpen = false"
     class="fixed inset-0 bg-primary/50 backdrop-blur-sm z-40 lg:hidden" x-cloak></div>

<!-- SideNavBar -->
<aside class="fixed left-0 top-0 h-screen w-[280px] flex flex-col bg-primary border-r border-outline-variant z-50 overflow-y-auto custom-scrollbar
       transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
    :class="sidebarOpen && 'translate-x-0'"
>
    <div class="p-lg">
```

## Livewire shared filtered query pattern
When filters (date range, staff, status) should affect **all** data on a page (KPIs, pagination, charts), build a single filtered query method and reuse it via `clone()`:
```php
class SalesHistory extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $staffFilter = '';

    protected function getFilteredQuery()
    {
        $query = Transaction::where('user_id', auth()->id());

        if ($this->dateFrom) {
            $query->whereDate('transaction_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('transaction_date', '<=', $this->dateTo);
        }
        if ($this->staffFilter) {
            $query->whereHas('user', fn($q) => $q->where('name', $this->staffFilter));
        }

        return $query;
    }

    public function render()
    {
        $filteredQuery = $this->getFilteredQuery();

        $transactions = $filteredQuery->clone()->latest()->paginate(10);
        $allFiltered = $filteredQuery->clone()->get();

        $totalRevenue = $allFiltered->sum('total_price');
        $totalTransactions = $allFiltered->count();
        $avgOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return view('livewire.user.sales-history', [
            'transactions' => $transactions,
            'realTotalRevenue' => $totalRevenue,
            'realTotalTransactions' => $totalTransactions,
            'realAvgOrderValue' => $avgOrderValue,
        ]);
    }
}
```
- **Always use `clone()`** before adding new constraints (like `latest()`, `paginate()`) — the base query object is mutable
- KPIs and chart data use the same filtered base, so they stay consistent when filters change

## Dynamic Chart.js with Livewire re-rendering
When using Chart.js inside a Livewire component, the canvas element gets morphed on re-render. You must destroy the existing chart before creating a new one, and reinitialize on Livewire updates:

```html
@push('scripts')
<script>
    function initSalesChart() {
        // Destroy existing chart to avoid "Canvas is already in use" errors
        const existingChart = Chart.getChart('salesPerformanceChart');
        if (existingChart) {
            existingChart.destroy();
        }

        const ctx = document.getElementById('salesPerformanceChart').getContext('2d');
        // ... gradient setup ...

        // Dynamic data from Livewire
        const chartLabels = @json($chartLabels);
        const chartValues = @json($chartValues);

        new Chart(ctx, { /* ... config using chartLabels/chartValues ... */ });
    }

    // Initialize on first load
    document.addEventListener('DOMContentLoaded', initSalesChart);

    // Reinitialize on Livewire navigation
    document.addEventListener('livewire:navigated', initSalesChart);

    // Reinitialize when Livewire morphs the chart container
    Livewire.hook('morph.updated', ({ el }) => {
        if (el.querySelector && el.querySelector('#salesPerformanceChart')) {
            initSalesChart();
        }
    });
</script>
@endpush
```
- `Chart.getChart(canvasId)` retrieves an existing chart instance for cleanup
- `@json($variable)` safely passes PHP arrays to JavaScript
- `Livewire.hook('morph.updated')` catches DOM morphs from Livewire re-renders
- Use `@push('scripts')` / `@endpush` instead of inline scripts so they're appended once