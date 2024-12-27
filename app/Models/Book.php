<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cocur\Slugify\Bridge\Laravel\SlugifyFacade;


class Book extends Model
{
    use HasFactory, SoftDeletes, Timestamp;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'books';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'picture',
        'author_id',
        'genre_id',
        'slug',
        'year_publication',
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
     * Define the relationship with the Author model.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Define the relationship with the Genre model.
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    
    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($book) {
            if (isset($book->name)) {
                $book->slug = SlugifyFacade::slugify($book->name);
            }
        });
    }

}
