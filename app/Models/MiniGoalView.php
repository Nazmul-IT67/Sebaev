<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiniGoalView extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'ip_address',
        'viewed_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'post_id' => 'integer',
    ];

    // Each view belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Each view belongs to a post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
