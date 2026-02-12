<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMovement extends Model
{

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }
}
