<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Scalar\MagicConst\Dir;

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

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'movies_actors', 'movie_id', 'actor_id');
    }

    public function directors()
    {
        return $this->belongsToMany(Director::class, 'movies_directors', 'movie_id', 'director_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
