<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'author_id',
        'album_id',
        'photo',
        'is_liked_by_me',
        'description',
        'comment_count',
        'like_count'
    ];

    public function author() {
        return $this->belongsTo('App\Models\User', 'author_id');
    }

    public function album() {
        return $this->belongsTo('App\Models\Album');
    }
}
