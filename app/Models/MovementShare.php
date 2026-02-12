<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementShare extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'movement_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
