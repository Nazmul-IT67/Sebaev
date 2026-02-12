<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementView extends Model
{
    protected $fillable = [
        'user_id',
        'movement_id',
        'ip_address',
        'viewed_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'movement_id' => 'integer',
    ];

    // MovementView belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // MovementView belongs to a Movement
    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }
}
