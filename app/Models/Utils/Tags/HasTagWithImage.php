<?php

namespace App\Models\Utils\Tags;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags;

trait HasTagWithImage
{
    use HasTags;

    public static function getTagClassName(): string
    {
        return TagWithImage::class;
    }

    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }
}
