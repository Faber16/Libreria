<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cocur\Slugify\Bridge\Laravel\SlugifyFacade;


/**
 * @OA\Schema(
 *     schema="Genre",
 *     title="Genre",
 *     description="Genre model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Fantasy"),
 *     @OA\Property(property="slug", type="string", example="fantasy"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T01:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
 * )
 */
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
