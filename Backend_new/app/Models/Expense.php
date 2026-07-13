<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'budget_line_id',
        'amount',
        'description',
        'proof_url',
        'status',
        'validated_by',
        'validated_at',
        'submitted_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the budget line this expense belongs to
     */
    public function budgetLine(): BelongsTo
    {
        return $this->belongsTo(BudgetLine::class, 'budget_line_id');
    }

    /**
     * Get the user who validated this expense
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get the user who submitted this expense
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Mark expense as validated
     */
    public function validate(User $user): void
    {
        $this->update([
            'status' => 'validated',
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);
    }

    /**
     * Mark expense as rejected
     */
    public function reject(User $user): void
    {
        $this->update([
            'status' => 'rejected',
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);
    }
}
