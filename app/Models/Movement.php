<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function countries()
    {
        return $this->belongsTo(Country::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'user_movements');
    }

    public function userMovements()
    {
        return $this->hasMany(UserMovement::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function views()
    {
        return $this->hasMany(MovementView::class);
    }

    public function limitedUserMovements()
    {
        return $this->hasMany(UserMovement::class)->limit(3)->with('user:id,avatar');
    }

    public function movementShare()
    {
        return $this->hasMany(MovementShare::class);
    }

    public function donationHistories()
    {
        return $this->hasMany(DonationHistory::class);
    }

    public function documents() {
        return $this->hasMany(MovementDocument::class);
    }
}
