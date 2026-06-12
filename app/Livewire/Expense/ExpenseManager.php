<?php

namespace App\Livewire\Expense;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('components.layouts.user')]
#[Title('Personal Expenses - Inventory Pro')]
class ExpenseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFilter = 'monthly'; // daily, weekly, monthly, yearly
    public $selected = [];
    public $selectAll = false;

    // fields for create / edit form
    public $expenseId = null;
    public $category_name = '';
    public $date = '';
    public $location = '';
    public $description = '';
    public $amount = '';
    public $showModal = false;
    public $isEdit = false;
    
    public $categoryNames = [];
    public array $chartData = [];

    protected $queryString = ['search', 'dateFilter'];

    public function updatedDateFilter()
    {
        $this->dispatch('expenses-updated', chartData: $this->prepareChartData());
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->expensesQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $this->selectAll = false;
    }

    // -------- CRUD Operations --------
    public function showCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $expense = Expense::findOrFail($id);
        $this->expenseId = $expense->id;
        $this->category_name = $expense->category->name ?? '';
        $this->date = $expense->date->format('Y-m-d');
        $this->location = $expense->location;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->isEdit = true;
        $this->showModal = true;
    }

    private function resetForm()
    {
        $this->expenseId = null;
        $this->category_name = '';
        $this->date = '';
        $this->location = '';
        $this->description = '';
        $this->amount = '';
    }

    public function saveExpense()
    {
        $this->validate([
            'category_name' => 'required|string|max:255',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
        ]);

        $category = ExpenseCategory::firstOrCreate(['name' => $this->category_name]);

        $data = [
            'category_id' => $category->id,
            'user_id' => auth()->id(),
            'date' => $this->date,
            'location' => $this->location,
            'description' => $this->description,
            'amount' => $this->amount,
        ];

        if ($this->isEdit) {
            $expense = Expense::findOrFail($this->expenseId);
            $expense->update($data);
        } else {
            Expense::create($data);
        }

        $this->showModal = false;
        $this->resetForm();
        $this->categoryNames = ExpenseCategory::pluck('name')->toArray();
        $this->dispatch('expenses-updated', chartData: $this->prepareChartData());
    }

    public function deleteExpense($id)
    {
        Expense::findOrFail($id)->delete();
        $this->dispatch('expenses-updated', chartData: $this->prepareChartData());
    }

    public function exportPdf()
    {
        $expenses = Expense::whereIn('id', $this->selected)->with('category')->get();
        $pdf = Pdf::loadView('expense.pdf', ['expenses' => $expenses]);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'expenses_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    public function mount()
    {
        $this->categoryNames = ExpenseCategory::pluck('name')->toArray();
    }

    public function render()
    {
        $expenses = $this->expensesQuery()->paginate(10);
        $this->chartData = $this->prepareChartData();
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('livewire.expense.expense-manager', [
            'expenses' => $expenses,
            'categories' => $categories,
        ]);
    }

    private function expensesQuery()
    {
        $query = Expense::with('category')
            ->where('user_id', Auth::id())
            ->when($this->search, fn($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->orderBy('date', 'desc');
        return $query;
    }

    private function prepareChartData()
    {
        // Aggregate total amount per period based on filter
        // Using PostgreSQL-compatible functions
        $groupBy = match($this->dateFilter) {
            'daily'   => "date::date",
            'weekly'  => "TO_CHAR(date, 'IYYY-IW')",
            'monthly' => "TO_CHAR(date, 'YYYY-MM')",
            'yearly'  => "TO_CHAR(date, 'YYYY')",
            default   => "date::date",
        };
        $records = Expense::where('user_id', Auth::id())
            ->selectRaw("{$groupBy} as period, SUM(amount) as total")
            ->groupByRaw($groupBy)
            ->orderBy('period')
            ->get();
        $labels = $records->pluck('period');
        $data = $records->pluck('total');
        return ['labels' => $labels, 'data' => $data];
    }
}
?>
