<?php

namespace App\Models\Utils\Tags;

use Illuminate\Support\Facades\Storage;
use Spatie\Tags\Tag;

/**
 * Class TagWithImage
 *
 * Extends the Spatie\Tags\Tag class to include an image field.
 */
class TagWithImage extends Tag
{
    protected $table = 'tags';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'type',
    ];

    /**
     * Get the URL of the tag's image.
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) { // Assuming 'image' is the field name in the tags table
            return Storage::url($this->image); // Adjust the path as necessary
        }
        return null; // Return null if no image is set
    }
}
