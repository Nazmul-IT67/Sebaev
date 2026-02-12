<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostShare extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
