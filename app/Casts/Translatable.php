<?php

namespace App\Casts;

use App\Serializers\Translatable as SerializersTranslatable;
use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @property string en
 * @property string ar
 */
class Translatable implements CastsAttributes
{
    /**
     * Cast the given value.
     * @param array<string, mixed> $attributes
     * @throws Exception
     */
    public function get($model, string $key, mixed $value, array $attributes): SerializersTranslatable
    {
        return new SerializersTranslatable($value);
    }

    /**
     * Prepare the given value for storage.
     * @param array<string, mixed> $attributes
     * @throws Exception
     */
    public function set($model, string $key, mixed $value, array $attributes): string|bool
    {
        if ($value instanceof SerializersTranslatable) {
            return $value->toJson();
        } else {
            return (new SerializersTranslatable($value))->toJson();
        }
    }
}
