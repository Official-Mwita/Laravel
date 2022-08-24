<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletionFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'FileName',
        'StoragePath',
        'information',
        'allow_download',
        'reason_denied',


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
