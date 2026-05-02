<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'type',
        'audience',
        'customBatch',
        'html',
        'expiry'
    ];

    public function batches()
    {
        return $this->belongsTo(batches::class, 'customBatch');
    }
}
