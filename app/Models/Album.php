<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
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
        'preview',
        'description',
    ];

    public function photos() {
        return $this->HasMany('App\Models\Photo');
    }

    public function author() {
        return $this->belongsTo('App\Models\User', 'author_id');
    }
}
