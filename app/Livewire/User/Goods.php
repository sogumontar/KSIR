<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Models\Transaction;
use App\Models\Good;

#[Layout('components.layouts.user')]
#[Title('Goods & Recipients - Inventory Pro')]
class Goods extends Component
{
    use WithPagination;

    public bool $showAddModal = false;
    public bool $showEditModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $recordToDelete = null;

    // Add form
    public ?int $goodId = null;
    public int $qty = 0;
    public float $price = 0.0;
    public string $recipientId = '';
    public string $status = 'pending';
    public string $dueDate = '';
    public $proofFile;

    // Edit form
    public ?int $editTransactionId = null;
    public string $editItemName = '';
    public int $editQty = 0;
    public float $editPrice = 0.0;
    public string $editRecipientId = '';
    public string $editStatus = 'pending';
    public string $editDueDate = '';

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
        $this->reset(['goodId', 'qty', 'price', 'recipientId', 'status', 'dueDate', 'proofFile']);
        $this->showAddModal = true;
    }

    public function updatedGoodId($value)
    {
        if ($value) {
            $good = Good::where('user_id', auth()->id())->find($value);
            if ($good) {
                $this->price = (float) $good->price;
                if ($this->qty <= 0) {
                    $this->qty = 1;
                }
            }
        } else {
            $this->price = 0.0;
        }
    }

    public function saveRecord()
    {
        $this->validate([
            'goodId' => 'required|exists:goods,id',
            'qty' => 'required|integer|min:1',
            'recipientId' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        $good = Good::where('user_id', auth()->id())->findOrFail($this->goodId);

        // Validate stock availability
        if ($good->stock < $this->qty) {
            $this->addError('qty', 'Insufficient stock in inventory. Available: ' . $good->stock);
            return;
        }

        // Deduct stock
        $good->decrement('stock', $this->qty);

        Transaction::create([
            'user_id' => auth()->id(),
            'good_id' => $good->id,
            'transaction_date' => now(),
            'item_name' => $good->name,
            'recipient_name' => $this->recipientId,
            'quantity' => $this->qty,
            'price' => $this->price,
            'total_price' => $this->qty * $this->price,
            'status' => $this->status,
            'due_date' => ($this->status === 'loan' && $this->dueDate) ? $this->dueDate : null,
        ]);

        $this->showAddModal = false;
        $this->reset(['goodId', 'qty', 'price', 'recipientId', 'status', 'dueDate', 'proofFile']);
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
        $this->editRecipientId = $tx->recipient_name ?? '';
        $this->editStatus = $tx->status ?? 'pending';
        $this->editDueDate = $tx->due_date ? \Carbon\Carbon::parse($tx->due_date)->format('Y-m-d') : '';
        $this->showEditModal = true;
    }

    public function updateRecord()
    {
        $this->validate([
            'editQty' => 'required|integer|min:1',
            'editPrice' => 'required|numeric|min:0',
            'editStatus' => 'required|string|max:50',
        ]);

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

            $transaction->update([
                'quantity' => $this->editQty,
                'price' => $this->editPrice,
                'total_price' => $this->editQty * $this->editPrice,
                'status' => $this->editStatus,
                'due_date' => ($this->editStatus === 'loan' && $this->editDueDate) ? $this->editDueDate : null,
            ]);
        }
        
        $this->showEditModal = false;
    }

    public function openView(int $id)
    {
        $tx = Transaction::where('user_id', auth()->id())->findOrFail($id);
        $this->viewRecord = [
            'date' => optional($tx->transaction_date)->format('M d, Y') ?? '-',
            'item' => $tx->item_name ?? '-',
            'recipient' => $tx->recipient_name ?? '-',
            'qty' => $tx->quantity ?? 0,
            'price' => (float) ($tx->price ?? 0),
            'total' => (float) ($tx->total_price ?? 0),
            'status' => ucfirst($tx->status ?? 'unknown'),
            'dueDate' => $tx->due_date ? \Carbon\Carbon::parse($tx->due_date)->format('M d, Y') : null,
            'proof' => 'Uploaded',
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
        
        return view('livewire.user.goods', [
            'transactions' => $transactions,
            'goodsList' => $goodsList,
        ]);
    }
}
