<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostResponseVideoComment extends Model
{

    protected $guarded = [];

    protected $hidden = [
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'post_id' => 'integer',
            'video_comment_id' => 'integer',
            'reply_id' => 'integer',
        ];
    }


    // A reply belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A reply belongs to a post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // A reply belongs to a parent video comment
    public function videoComment()
    {
        return $this->belongsTo(VideoComment::class);
    }

    // A reply can have nested replies (threaded comments)
    public function replies()
    {
        return $this->hasMany(PostResponseVideoComment::class, 'reply_id');
    }

    // A reply may belong to another reply (if it's a nested reply)
    public function parentReply()
    {
        return $this->belongsTo(PostResponseVideoComment::class, 'video_comment_id');
    }
}
