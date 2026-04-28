<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStudent extends Model
{
    use softDeletes;
    protected $fillable = [
        'feeId',
        'userId',
        'created_by',
        'updated_by',
        'deleted_by',
        'paid_amount',
        'payment'
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
