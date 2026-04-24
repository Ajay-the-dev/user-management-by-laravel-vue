<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Faker\Factory as Faker;


class Fee extends Model
{
    use SoftDeletes,HasFactory;
    protected $fillable = ['batchId', 'type', 'amount', 'status', 'created_by', 'updated_by', 'deleted_by','due'];
    protected $casts = ['amount' => 'float'];


    public function batches()
    {
        return $this->belongsTo(batches::class, 'batchId');
    }
}
