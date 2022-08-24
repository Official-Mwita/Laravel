<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderCancellation;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'academic_level',
        'subject_name',
        'service',
        'type_of_paper',
        'description',
        'reference_style',
        'pages',
        'hours',
        'spacing',
        'trackId',
        'cancelled',
        'progress_status',
        'progress_class',
    ];

    protected $hidden = [
        'id',
        'user_id'
    ];

    public function cancellation()
    {
        return $this->hasMany(OrderCancellation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ownedBy(User $user)
    {
        return $user->id === $this->user_id;
    }

    public function isCancelled()
    {
        return $this->cancelled === 1;
    }

    public function support_files()
    {
        return $this->hasMany(SupportFile::class);
    }

    public function completion_files()
    {
        return $this->hasMany(CompletionFiles::class);
    }



}
