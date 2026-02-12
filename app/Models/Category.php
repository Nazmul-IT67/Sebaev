<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }


    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
