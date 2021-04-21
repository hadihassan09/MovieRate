<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trailer extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trailer',
        'movie_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'movie_id'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
