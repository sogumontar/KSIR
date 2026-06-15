<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Models\Transaction;
use App\Models\Good;

#[Layout('components.layouts.user')]
#[Title('Sales Record - Inventory Pro')]
class Goods extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showAddModal = false;
    public bool $showEditModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $recordToDelete = null;

    // Add form
    public ?int $goodId = null;
    public int $qty = 0;
    public float $price = 0.0;
    public float $sellPrice = 0.0;
    public string $recipientId = '';
    public string $status = 'pending';
    public string $transactionDate = '';
    public string $dueDate = '';
    public string $salesType = 'offline';
    public ?string $salesCode = null;
    public $proofFile;

    // Edit form
    public ?int $editTransactionId = null;
    public string $editItemName = '';
    public int $editQty = 0;
    public float $editPrice = 0.0;
    public float $editSellPrice = 0.0;
    public string $editRecipientId = '';
    public string $editStatus = 'pending';
    public string $editTransactionDate = '';
    public string $editDueDate = '';
    public string $editSalesType = 'offline';
    public ?string $editSalesCode = null;
    public $editProofFile;
    public ?string $existingProof = null;

    // View record
    public array $viewRecord = [];

    public array $selected = [];

    #[Url]
    public string $statusFilter = '';

    public function selectAll()
    {
        $query = Transaction::where('user_id', auth()->id());

        if ($this->statusFilter) {
            if ($this->statusFilter === 'in_progress') {
                $query->whereIn('status', ['pending', 'transit']);
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        $allIds = $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selected = $allIds;
    }

    public function clearSelection()
    {
        $this->selected = [];
    }

    public function exportSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        $transactions = Transaction::whereIn('id', $this->selected)
            ->where('user_id', auth()->id())
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.user.transactions-pdf', [
            'transactions' => $transactions,
        ]);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'transactions-export-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function openAdd()
    {
        $this->reset(['goodId', 'qty', 'price', 'sellPrice', 'recipientId', 'status', 'transactionDate', 'dueDate', 'proofFile', 'salesType', 'salesCode']);
        $this->transactionDate = now()->format('Y-m-d');
        $this->showAddModal = true;
    }

    public function updatedGoodId($value)
    {
        if ($value) {
            $good = Good::where('user_id', auth()->id())->find($value);
            if ($good) {
                $this->price = (float) $good->price;
                $this->sellPrice = (float) $good->price;
                if ($this->qty <= 0) {
                    $this->qty = 1;
                }
            }
        } else {
            $this->price = 0.0;
            $this->sellPrice = 0.0;
        }
    }

    public function saveRecord()
    {
        $requiresProof = in_array($this->status, ['delivered', 'loan']);

        $rules = [
            'goodId' => 'required|exists:goods,id',
            'qty' => 'required|integer|min:1',
            'sellPrice' => 'required|numeric|min:0',
            'recipientId' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'salesType' => 'required|in:offline,online',
            'salesCode' => 'required_if:salesType,online|nullable|string|max:255',
            'transactionDate' => 'required|date',
            'proofFile' => $requiresProof
                ? 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
                : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];

        if ($requiresProof && !$this->proofFile) {
            $this->addError('proofFile', 'Proof of delivery is required when status is delivered or loan.');
        }

        $this->validate($rules);

        $good = Good::where('user_id', auth()->id())->findOrFail($this->goodId);

        // Validate stock availability
        if ($good->stock < $this->qty) {
            $this->addError('qty', 'Insufficient stock in inventory. Available: ' . $good->stock);
            return;
        }

        // Deduct stock
        $good->decrement('stock', $this->qty);

        $proofPath = null;
        if ($this->proofFile) {
            $proofPath = $this->proofFile->store('proofs', 'public');
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'good_id' => $good->id,
            'transaction_date' => $this->transactionDate,
            'item_name' => $good->name,
            'recipient_name' => $this->recipientId,
            'quantity' => $this->qty,
            'price' => $this->price,
            'sell_price' => $this->sellPrice,
            'total_price' => $this->qty * $this->sellPrice,
            'profit' => ($this->sellPrice - $this->price) * $this->qty,
            'status' => $this->status,
            'sales_type' => $this->salesType,
            'sales_code' => $this->salesType === 'online' ? $this->salesCode : null,
            'due_date' => ($this->status === 'loan' && $this->dueDate) ? $this->dueDate : null,
            'proof_of_delivery' => $proofPath,
        ]);

        $this->showAddModal = false;
        $this->reset(['goodId', 'qty', 'price', 'sellPrice', 'recipientId', 'status', 'transactionDate', 'dueDate', 'proofFile', 'salesType', 'salesCode']);
    }

    public function openEdit(int $id)
    {
        $tx = Transaction::where('user_id', auth()->id())->findOrFail($id);

        // Prevent editing if the associated good is soft-deleted
        if ($tx->good_id) {
            $good = Good::withTrashed()->find($tx->good_id);
            if ($good && $good->trashed()) {
                session()->flash('error', 'Cannot edit a transaction with a deleted good.');
                return;
            }
        }

        $this->editTransactionId = $id;
        $this->editItemName = $tx->item_name ?? '';
        $this->editQty = (int) $tx->quantity;
        $this->editPrice = (float) $tx->price;
        $this->editSellPrice = (float) ($tx->sell_price ?? $tx->price);
        $this->editRecipientId = $tx->recipient_name ?? '';
        $this->editStatus = $tx->status ?? 'pending';
        $this->editTransactionDate = $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d') : '';
        $this->editDueDate = $tx->due_date ? \Carbon\Carbon::parse($tx->due_date)->format('Y-m-d') : '';
        $this->editSalesType = $tx->sales_type ?? 'offline';
        $this->editSalesCode = $tx->sales_code;
        $this->existingProof = $tx->proof_of_delivery;
        $this->editProofFile = null;
        $this->showEditModal = true;
    }

    public function updateRecord()
    {
        $requiresProof = in_array($this->editStatus, ['delivered', 'loan']);

        $rules = [
            'editQty' => 'required|integer|min:1',
            'editPrice' => 'required|numeric|min:0',
            'editSellPrice' => 'required|numeric|min:0',
            'editStatus' => 'required|string|max:50',
            'editSalesType' => 'required|in:offline,online',
            'editSalesCode' => 'required_if:editSalesType,online|nullable|string|max:255',
            'editTransactionDate' => 'required|date',
            'editProofFile' => $requiresProof && !$this->existingProof
                ? 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
                : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];

        if ($requiresProof && !$this->existingProof && !$this->editProofFile) {
            $this->addError('editProofFile', 'Proof of delivery is required when status is delivered or loan.');
        }

        $this->validate($rules);

        if ($this->editTransactionId) {
            $transaction = Transaction::where('user_id', auth()->id())->findOrFail($this->editTransactionId);

            // Check if the associated good is soft-deleted
            if ($transaction->good_id) {
                $good = Good::withTrashed()->find($transaction->good_id);
                if ($good && $good->trashed()) {
                    $this->addError('editQty', 'Cannot update transaction of a deleted good.');
                    return;
                }

                if ($good) {
                    $diff = $this->editQty - $transaction->quantity;

                    // Check if stock is sufficient if increasing quantity
                    if ($diff > 0 && $good->stock < $diff) {
                        $this->addError('editQty', 'Insufficient stock in inventory. Available: ' . $good->stock);
                        return;
                    }

                    // Adjust stock
                    $good->decrement('stock', $diff);
                }
            }

            $proofPath = $transaction->proof_of_delivery;
            if ($this->editProofFile) {
                if ($proofPath) {
                    \Storage::disk('public')->delete($proofPath);
                }
                $proofPath = $this->editProofFile->store('proofs', 'public');
            } elseif (!$this->existingProof && $proofPath) {
                \Storage::disk('public')->delete($proofPath);
                $proofPath = null;
            }

            $transaction->update([
                'quantity' => $this->editQty,
                'price' => $this->editPrice,
                'sell_price' => $this->editSellPrice,
                'total_price' => $this->editQty * $this->editSellPrice,
                'profit' => ($this->editSellPrice - $this->editPrice) * $this->editQty,
                'status' => $this->editStatus,
                'sales_type' => $this->editSalesType,
                'sales_code' => $this->editSalesType === 'online' ? $this->editSalesCode : null,
                'transaction_date' => $this->editTransactionDate,
                'due_date' => ($this->editStatus === 'loan' && $this->editDueDate) ? $this->editDueDate : null,
                'proof_of_delivery' => $proofPath,
            ]);
        }

        $this->showEditModal = false;
    }

    public function openView(int $id)
    {
        $tx = Transaction::where('user_id', auth()->id())->findOrFail($id);
        $good = $tx->good_id ? Good::withTrashed()->find($tx->good_id) : null;
        $this->viewRecord = [
            'date' => optional($tx->transaction_date)->format('M d, Y') ?? '-',
            'item' => $tx->item_name ?? '-',
            'unitType' => $good ? ($good->unit_type ? ucfirst($good->unit_type) : '-') : '-',
            'recipient' => $tx->recipient_name ?? '-',
            'qty' => $tx->quantity ?? 0,
            'price' => (float) ($tx->price ?? 0),
            'sellPrice' => (float) ($tx->sell_price ?? $tx->price ?? 0),
            'total' => (float) ($tx->total_price ?? 0),
            'profit' => (float) ($tx->profit ?? 0),
            'status' => ucfirst($tx->status ?? 'unknown'),
            'salesType' => ucfirst($tx->sales_type ?? 'offline'),
            'salesCode' => $tx->sales_code ?? '-',
            'dueDate' => $tx->due_date ? \Carbon\Carbon::parse($tx->due_date)->format('M d, Y') : null,
            'proof' => $tx->proof_of_delivery,
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
            $tx = Transaction::where('user_id', auth()->id())->findOrFail($this->recordToDelete);

            // Check if the associated good is soft-deleted
            if ($tx->good_id) {
                $good = Good::withTrashed()->find($tx->good_id);
                // Only recover stock if the good is NOT soft-deleted
                if ($good && !$good->trashed()) {
                    $good->increment('stock', $tx->quantity);
                }
            }

            $tx->delete();
        }
        $this->showDeleteModal = false;
        $this->recordToDelete = null;
    }

    public function mount()
    {
    }

    public function render()
    {
        $query = Transaction::where('user_id', auth()->id());

        if ($this->statusFilter) {
            if ($this->statusFilter === 'in_progress') {
                $query->whereIn('status', ['pending', 'transit']);
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        $transactions = $query->latest()->paginate(10);
        $goodsList = Good::where('user_id', auth()->id())->latest()->get();

        $loanSummary = Transaction::where('user_id', auth()->id())
            ->where('status', 'loan')
            ->selectRaw('recipient_name, SUM(total_price) as total_loan_amount, COUNT(*) as loan_count, MIN(due_date) as nearest_due_date')
            ->groupBy('recipient_name')
            ->orderByDesc('total_loan_amount')
            ->get()
            ->map(function ($row) {
                $loans = Transaction::where('user_id', auth()->id())
                    ->where('status', 'loan')
                    ->where('recipient_name', $row->recipient_name)
                    ->orderBy('due_date', 'asc')
                    ->get()
                    ->map(fn ($tx) => [
                        'id' => $tx->id,
                        'item_name' => $tx->item_name,
                        'quantity' => $tx->quantity,
                        'total_price' => (float) $tx->total_price,
                        'transaction_date' => optional($tx->transaction_date)->format('M d, Y'),
                        'due_date' => $tx->due_date ? \Carbon\Carbon::parse($tx->due_date)->format('M d, Y') : null,
                        'is_overdue' => $tx->due_date && \Carbon\Carbon::parse($tx->due_date)->isPast(),
                    ]);

                return [
                    'name' => $row->recipient_name,
                    'total_loan_amount' => (float) $row->total_loan_amount,
                    'loan_count' => (int) $row->loan_count,
                    'nearest_due_date' => $row->nearest_due_date ? \Carbon\Carbon::parse($row->nearest_due_date)->format('M d, Y') : null,
                    'nearest_due_date_is_overdue' => $row->nearest_due_date ? \Carbon\Carbon::parse($row->nearest_due_date)->isPast() : false,
                    'loans' => $loans,
                ];
            })
            ->values();

        return view('livewire.user.goods', [
            'transactions' => $transactions,
            'goodsList' => $goodsList,
            'loanSummary' => $loanSummary,
        ]);
    }
}