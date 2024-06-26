<?php

namespace App\Models\Transactions;

use App\Enums\TransactionType;
use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([TenantScope::class])]
class Transaction extends Model
{
    use BelongsToUser, HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'transaction_type',
        'amount',
        'date',
        'finished',
        'description',
        'account_id',
        'category_id',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'transaction_type' => TransactionType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
