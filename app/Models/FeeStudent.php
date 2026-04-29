<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function fee()
    {
        return $this->belongsTo(Fee::class, 'feeId');
    }

    
    public function student()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
