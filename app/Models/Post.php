<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
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
            'category_id' => 'integer',
            'sub_category_id' => 'integer',
            'movement_id' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->hasMany(ReportPost::class);
    }

    public function videoComments()
    {
        return $this->hasMany(VideoComment::class, 'post_id');
    }

    public function miniGoalViews()
    {
        return $this->hasMany(MiniGoalView::class);
    }

    public function video_comments()
    {
        return $this->hasMany(VideoComment::class);
    }

    public function postShare()
    {
        return $this->hasMany(PostShare::class);
    }

    public function videoCommentReplies()
    {
        return $this->hasMany(PostResponseVideoComment::class);
    }
}
