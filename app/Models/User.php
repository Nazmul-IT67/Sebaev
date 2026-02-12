<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Namu\WireChat\Traits\Chatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Chatable, HasRoles;

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Return the primary key of the user (id)
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'provider',
        'provider_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_premium' => 'boolean',
            'id' => 'integer',
            'agree_to_terms' => 'boolean',
        ];
    }

    /**
     * Define the relationship between User and OnBoding.
     */
    public function information(): HasOne
    {
        return $this->hasOne(UserInformation::class);
    }

    public function interests()
    {
        return $this->hasMany(UserInterest::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function participatedMovements()
    {
        return $this->belongsToMany(Movement::class, 'user_movements');
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

    public function reports()
    {
        return $this->hasMany(ReportPost::class);
    }

    public function videoComments()
    {
        return $this->hasMany(VideoComment::class);
    }

    public function movementViews()
    {
        return $this->hasMany(MovementView::class);
    }

    public function miniGoalViews()
    {
        return $this->hasMany(MiniGoalView::class);
    }

    public function donationHistories()
    {
        return $this->hasMany(DonationHistory::class);
    }

    public function firebaseTokens()
    {
        return $this->hasOne(FirebaseToken::class);
    }

    public function videoCommentReplies()
    {
        return $this->hasMany(PostResponseVideoComment::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'user_interests', 'user_id', 'sub_category_id');
    }

}
