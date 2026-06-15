<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['group_expense_id', 'user_id', 'owed_amount'])]
class GroupExpenseShare extends Model
{
    protected function casts(): array
    {
        return [
            'owed_amount' => 'decimal:2',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(GroupExpense::class, 'group_expense_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
