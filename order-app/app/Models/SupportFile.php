<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'FileName',
        'StoragePath',
        'information',
    ];

    protected $hidden = [
        'StoragePath',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
