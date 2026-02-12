<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovementResponseVideoComment extends Model
{
    protected $table = 'm_r_video_comments'; // Shorter table name

    protected $fillable = [
        'user_id',
        'movement_id',
        'm_r_video_id', // Shorter column name
        'reply_id',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function video()
    {
        return $this->belongsTo(MovementResponseVideo::class, 'm_r_video_id'); // Shorter referenced table
    }

    public function replies()
    {
        return $this->hasMany(MovementResponseVideoComment::class, 'reply_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MovementResponseVideoComment::class, 'm_r_video_id', 'id');
    }
}
