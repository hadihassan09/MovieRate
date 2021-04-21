<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'poster',
        'release_date'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movies_genres');
    }

    public function trailers()
    {
        return $this->hasMany(Trailer::class);
    }
}