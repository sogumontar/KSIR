<!-- resources/views/livewire/expense/expense-manager.blade.php -->
<div x-data="{ showModal: @entangle('showModal') }">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-md mb-8">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">Personal Expenses</h2>
        </div>
        
        <div class="flex flex-col md:flex-row items-end gap-md">
            <!-- Date Range Filter -->
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <div class="flex items-center gap-2 bg-white border border-slate-300 rounded-lg px-3 py-1.5 h-[42px]">
                    <span class="material-symbols-outlined text-slate-400 text-sm">calendar_today</span>
                    <input wire:model.live="dateFrom" type="date" class="border-none p-0 text-sm focus:ring-0 w-32 bg-transparent">
                    <span class="text-slate-400 text-sm">to</span>
                    <input wire:model.live="dateTo" type="date" class="border-none p-0 text-sm focus:ring-0 w-32 bg-transparent">
                </div>
                <button wire:click="resetTableFilters" class="btn-ghost px-3 h-[42px] border border-slate-300 bg-white" title="Reset Filters">
                    <span class="material-symbols-outlined">restart_alt</span>
                </button>
            </div>

            <div class="flex items-center gap-md flex-col sm:flex-row flex-wrap w-full md:w-auto">
                <input type="text" class="form-input bg-white appearance-none text-sm py-2.5 px-4 rounded-lg border border-slate-300 w-full sm:w-auto min-h-[42px]" placeholder="Search description…" wire:model.debounce.500ms="search">
                <select class="form-input bg-white appearance-none text-sm py-2.5 px-4 rounded-lg border border-slate-300 w-full sm:w-auto min-h-[42px]" wire:model.live="dateFilter">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
                <button wire:click="showCreateModal" class="btn-primary gap-2 w-full sm:w-auto justify-center">
                    <span class="material-symbols-outlined">add</span>
                    Add Expense
                </button>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card-surface overflow-hidden mb-8 p-6"
         wire:ignore
         x-data="{
             chart: null,
             initChart(data) {
                 if (!data || !data.labels) return;
                 const ctx = document.getElementById('expensesChart');
                 if (!ctx) return;
                 if (this.chart) { this.chart.destroy(); this.chart = null; }
                 this.chart = new Chart(ctx.getContext('2d'), {
                     type: 'line',
                     data: {
                         labels: data.labels,
                         datasets: [{
                             label: 'Expenses',
                             data: data.data,
                             borderColor: '#3b82f6',
                             backgroundColor: 'rgba(59, 130, 246, 0.1)',
                             fill: true,
                             tension: 0.3,
                             borderWidth: 2,
                             pointBackgroundColor: '#ffffff',
                             pointBorderColor: '#3b82f6',
                             pointRadius: 4,
                         }],
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         scales: { y: { beginAtZero: true } },
                         plugins: { legend: { display: false } },
                     },
                 });
             }
         }"
         x-init="
             initChart($wire.chartData);
             $watch('$wire.chartData', value => {
                 initChart(value);
             });
         ">
        <div class="h-64">
            <canvas id="expensesChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card-surface overflow-hidden mb-8">
        <!-- Bulk Actions Bar -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-2 sm:gap-4">
            @if(count($selected) > 0)
            <span class="text-sm text-slate-600 font-medium">{{ count($selected) }} selected</span>
            @endif
            <div class="sm:ml-auto w-full sm:w-auto">
                <button wire:click="exportPdf" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg text-sm font-medium hover:opacity-90 transition-all w-full sm:w-auto justify-center {{ count($selected) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($selected) === 0 ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    Export Selected (PDF)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr>
                        <th class="table-header font-label-lg text-label-lg w-12"><input type="checkbox" wire:model.live="selectAll" class="form-checkbox rounded" /></th>
                        @foreach(['date' => 'Date', 'category_id' => 'Category', 'description' => 'Description', 'location' => 'Location', 'amount' => 'Amount'] as $col => $label)
                            <th class="table-header font-label-lg text-label-lg cursor-pointer hover:bg-slate-200 transition-colors" wire:click="sort('{{ $col }}')">
                                <div class="flex items-center gap-1 {{ $label === 'Amount' ? 'justify-end' : '' }}">
                                    {{ $label }}
                                    @if($sortColumn === $col)
                                        <span class="material-symbols-outlined text-sm">{{ $sortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                    @endif
                                </div>
                            </th>
                        @endforeach
                        <th class="table-header font-label-lg text-label-lg text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="font-body-md text-body-md text-slate-700">
                    @forelse($expenses as $expense)
                    <tr class="table-row-zebra border-b border-slate-200" wire:key="expense-{{ $expense->id }}">
                        <td class="table-cell">
                            <input type="checkbox" wire:model.live="selected" value="{{ $expense->id }}" class="form-checkbox rounded" />
                        </td>
                        <td class="table-cell">{{ $expense->date->format('M d, Y') }}</td>
                        <td class="table-cell font-semibold text-slate-900">{{ $expense->category->name ?? '-' }}</td>
                        <td class="table-cell">{{ $expense->description }}</td>
                        <td class="table-cell">{{ $expense->location }}</td>
                        <td class="table-cell text-right font-semibold">Rp{{ number_format($expense->amount, 0) }}</td>
                        <td class="table-cell text-center">
                            <div class="flex justify-center gap-2">
                                <button wire:click="showEditModal({{ $expense->id }})" class="btn-icon" title="Edit">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <button wire:click="deleteExpense({{ $expense->id }})" onclick="return confirm('Are you sure you want to delete this expense?');" class="btn-icon text-error hover:bg-error-container hover:text-error" title="Delete">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center p-8 text-slate-500">
                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">receipt_long</span>
                            No expenses found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between text-slate-600 font-label-md text-label-md gap-sm">
            <span>Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} records</span>
            <div class="flex gap-2">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    <!-- Modal: Add/Edit Expense -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showModal" style="display: none;" x-cloak>
        <!-- Overlay -->
        <div @click="showModal = false; @this.set('showModal', false)" class="fixed inset-0 modal-overlay" x-show="showModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-2xl rounded-xl modal-content flex flex-col max-h-[90vh]" x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-transition:leave-start="opacity-100 scale-100 translate-y-0">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0" x-text="$wire.isEdit ? 'Edit Expense' : 'Add Expense'"></h3>
                <button @click="showModal = false; @this.set('showModal', false)" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body (Scrollable) -->
            <form wire:submit.prevent="saveExpense" class="flex flex-col overflow-hidden h-full">
                <div class="p-8 overflow-y-auto font-body-md text-body-md space-y-6 flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Select Category -->
                        <div class="col-span-1 md:col-span-2" x-data="{
                            open: false,
                            search: @entangle('category_name'),
                            get categories() { return $wire.categoryNames; },
                            get filtered() {
                                if (!this.search) return this.categories;
                                return this.categories.filter(c => c.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            selectCategory(cat) {
                                this.search = cat;
                                this.open = false;
                            }
                        }">
                            <label class="form-label">Category <span class="text-error">*</span></label>
                            <div class="relative">
                                <input wire:model="category_name" @focus="open = true" @click.away="open = false" autocomplete="off" class="form-input bg-white w-full pr-10" placeholder="Type or click to select...">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                    <span class="material-symbols-outlined">expand_more</span>
                                </div>
                                <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto" x-cloak>
                                    <template x-for="cat in filtered" :key="cat">
                                        <div @click="selectCategory(cat)" class="px-4 py-2.5 hover:bg-slate-50 cursor-pointer text-slate-700 font-medium transition-colors" x-text="cat"></div>
                                    </template>
                                    <div x-show="filtered.length === 0 && search" class="px-4 py-2.5 bg-secondary-container/30 text-slate-700 text-sm">
                                        Will create new category: <span class="font-bold text-primary" x-text="search"></span>
                                    </div>
                                    <div x-show="filtered.length === 0 && !search" class="px-4 py-2.5 text-slate-500 text-sm italic">
                                        Start typing to create a new category
                                    </div>
                                </div>
                            </div>
                            @error('category_name') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <!-- Date -->
                        <div>
                            <label class="form-label">Date <span class="text-error">*</span></label>
                            <input wire:model="date" class="form-input bg-white w-full" type="date">
                            @error('date') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <!-- Amount -->
                        <div>
                            <label class="form-label">Amount (Rp) <span class="text-error">*</span></label>
                            <input wire:model="amount" class="form-input w-full" min="0" placeholder="0.00" step="0.01" type="number">
                            @error('amount') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <!-- Location -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="form-label">Location</label>
                            <input wire:model="location" class="form-input bg-white w-full" placeholder="e.g., Office Supplies Store" type="text">
                            @error('location') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <!-- Description -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="form-label">Description</label>
                            <textarea wire:model="description" class="form-input bg-white w-full" rows="3" placeholder="Additional details..."></textarea>
                            @error('description') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="p-6 border-t border-slate-200 flex justify-end gap-4 bg-slate-50 rounded-b-xl">
                    <button type="button" @click="showModal = false; @this.set('showModal', false)" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary gap-2">
                        <span class="material-symbols-outlined">save</span>
                        Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@endpush
