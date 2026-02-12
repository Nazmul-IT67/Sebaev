<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
    ];

    protected $hidden = [
        'status',
        'created_at',
        'updated_at',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function userInterests()
    {
        return $this->hasMany(UserInterest::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_interests', 'sub_category_id', 'user_id');
    }

}
