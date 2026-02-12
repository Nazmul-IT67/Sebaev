<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationHistory extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'movement_id' => 'integer',
    ];

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
