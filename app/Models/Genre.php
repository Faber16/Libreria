<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cocur\Slugify\Bridge\Laravel\SlugifyFacade;

class Genre extends Model
{
    use HasFactory, SoftDeletes, Timestamp;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'genres';


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];


    /**
     * Define the relationship with the Book model.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($genre) {
            if (isset($genre->name)) {
                $genre->slug = SlugifyFacade::slugify($genre->name);
            }
        });
    }
}
