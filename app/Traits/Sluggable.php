<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin Model
 */
trait Sluggable
{
    private static function generateSlug(Model $item): void
    {
        foreach ($item->sluggable() as $sluggables) {
            $separator = $sluggables['separator'] ?? "-";

            if (!is_callable($sluggables['col'])) {
                $slug = str("{$item->{"{$sluggables['col']}"}}")
                    ->kebab()
                    ->singular()
                    ->lower()
                    ->replace('-', $separator)
                    ->toString();
            } else {
                $slug = $sluggables['col']();
            }

            $notUnique = self::checkForUniqueness($sluggables, $slug, $item);

            $loopIndex = 0;
            while ($notUnique) {
                $num = $item?->id + $loopIndex;
                $newSlug = "{$slug}{$separator}{$num}";
                $notUnique = self::checkForUniqueness($sluggables, $newSlug, $item);
                $loopIndex++;
            }

            $item->{"{$sluggables['slug_col']}"} = $newSlug ?? $slug;
        }
    }

    protected static function booted(): void
    {
        self::creating(function (Model $item) {
            self::generateSlug($item);
        });
    }

    /**
     * @return array{array{col:string , slug_col:string , unique_cols:array|null , unique_cond:callable|null}}
     */
    public function sluggable(): array
    {
        return [];
    }

    /**
     * @param array{col:string , slug_col:string , unique_cols:array|null , unique_cond:callable|null} $sluggables
     * @param string                                                                                   $slug
     * @param Model                                                                                    $item
     * @return bool
     */
    private static function checkForUniqueness(array $sluggables, string $slug, Model $item): bool
    {
        if (!isset($sluggables['unique_cond'])) {
            return self::query()->where("{$sluggables['slug_col']}", $slug)->exists();
        }

        return (bool)$sluggables["unique_cond"]($slug);
    }
}
