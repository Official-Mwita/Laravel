<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowDownload extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip_address',
        'user_agent',
        'order_id',
        'user_host',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
