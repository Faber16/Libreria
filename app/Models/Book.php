<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cocur\Slugify\Bridge\Laravel\SlugifyFacade;


/**
 * @OA\Schema(
 *     schema="Book",
 *     title="Book",
 *     description="Book model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="The Great Gatsby"),
 *     @OA\Property(property="author_id", type="integer", example=1),
 *     @OA\Property(property="genre_id", type="integer", example=2),
 *     @OA\Property(property="year_publication", type="integer", example=1925),
 *     @OA\Property(property="picture", type="string", nullable=true, example="images/0b12345c-678d-90ef-a123-456789abcdef.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T01:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 */
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
