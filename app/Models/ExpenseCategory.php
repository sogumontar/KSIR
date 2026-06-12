<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class ExpenseCategory extends Model
{
    use SoftDeletes;

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
?>
