<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema(
 *     schema="Author",
 *     type="object",
 *     title="Author",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="full_name", type="string", example="John Doe"),
 *         @OA\Property(property="alias", type="string", example="JD"),
 *         @OA\Property(property="initials", type="string", example="J.D."),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T01:00:00Z"),
 *     }
 * )
 */
class Author extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'alias',
        'initials',
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

        static::saving(function ($author) {
            if (isset($author->full_name)) {
                $author->initials = implode('.', array_map(
                    fn($word) => mb_strtoupper(mb_substr($word, 0, 1)), 
                    explode(' ', $author->full_name)
                )) . '.';
            }
        });
    }
}
