<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStudent extends Model
{
    protected $fillable = [
        'feeId',
        'userId',
        'created_by',
        'updated_by',
        'deleted_by',
        'paid_amount',
    ];

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class, 'feeId');
    }

    
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
