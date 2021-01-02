<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'cover',
        'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     *  Get the albums for the user as author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function albums()
    {
        return $this->hasMany('App\Models\Album', 'author_id');
    }

    /**
     * Get the photos for the user as author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany('App\Models\Photo', 'author_id');
    }

    /**
     * Get the comments for the user as author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Models\Comments', 'author_id');
    }

    /**
     * The socials that belong to the user.
     */
    public function socials()
    {
        return $this->belongsToMany('App\Models\Social', 'author_social', 'author_id', 'social_id')->withPivot('link');
    }

    /**
     * The socials that belong to the user.
     */
    public function likes()
    {
        return $this->belongsToMany('App\Models\Photo', 'likes', 'author_id', 'photo_id')->as('liked_me');
    }
}
