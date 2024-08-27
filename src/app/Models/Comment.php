<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'rate',
        'comment',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
