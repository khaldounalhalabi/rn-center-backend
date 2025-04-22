<?php

namespace App\Casts;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IntervalCast implements CastsAttributes
{
    /**
     * Cast the given value.
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): CarbonInterval
    {
        $data = json_decode($value, true) ?? [
            "years" => 0,
            "months" => 0,
            "weeks" => 0,
            "days" => 0,
            "hours" => 0,
            "minutes" => 0,
            "seconds" => 0,
            "microseconds" => 0,
        ];

        return $this->getIntervalInstance($data);
    }

    /**
     * Prepare the given value for storage.
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_array($value)) {
            return json_encode(
                $this->getIntervalInstance($value)
                    ->toArray()
            );
        }

        if (is_string($value) && Str::isJson($value)) {
            $data = json_decode($value, true);
            return json_encode(
                $this->getIntervalInstance($data)
                    ->toArray()
            );
        }

        if ($value instanceof CarbonInterval) {
            return json_encode($value->toArray());
        }

        return $value;
    }

    /**
     * @param array $value
     * @return CarbonInterval
     */
    private function getIntervalInstance(array $value): CarbonInterval
    {
        return CarbonInterval::years($value['years'] ?? 0)
            ->months($value['months'] ?? 0)
            ->weeks($value['weeks'] ?? 0)
            ->days($value['days'] ?? 0)
            ->hours($value['hours'] ?? 0)
            ->minutes($value['minutes'] ?? 0)
            ->seconds($value['seconds'] ?? 0)
            ->microseconds($value['microseconds'] ?? 0);
    }
}
