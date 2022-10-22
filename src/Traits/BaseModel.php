<?php

namespace Novacio\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * BaseModel
 * 
 * @property int $id
 * @property ?string $slug
 * @property string $model
 * @property Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 */
trait BaseModel
{
    use HasFactory;
    use HasSlug;
    use Routes;
    use Searchable;
    use SoftDeletes;

    public function resolveRouteBinding($value, $field = "slug")
    {
        return $this->where($field, $value)->firstOrFail();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom("title")
            ->saveSlugsTo("slug");
    }

    public function getRouteKeyName(): string
    {
        return "slug";
    }

    public static function findSlug(string|array $slug): Model|Collection|null
    {
        if (is_string($slug)) {
            return static::where("slug", $slug)->first();
        } else if (is_array($slug)) {
            return static::whereIn("slug", $slug)->get();
        }
    }
}
