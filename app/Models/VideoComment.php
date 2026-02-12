<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    protected $guarded = [];

    protected $hidden = [
        // 'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'post_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function replies()
    {
        return $this->hasMany(PostResponseVideoComment::class, 'reply_id');
    }

    public function parentComment()
    {
        return $this->belongsTo(PostResponseVideoComment::class, 'video_comment_id');
    }
}
