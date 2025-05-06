<?php

namespace App\Repositories;

use App\Models\Payrun;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Payrun>
 */
class PayrunRepository extends BaseRepository
{
    protected string $modelClass = Payrun::class;

    public function checkForPeriodOverlap(string $from, string $to): bool
    {
        return Payrun::where(function (Builder $query) use ($to, $from) {
            $query->where(function (Builder $query) use ($from) {
                $query->where('from', '<=', $from)
                    ->where('to', '>=', $from);
            })->orWhere(function (Builder $query) use ($to) {
                $query->where('from', '<=', $to)
                    ->where('to', '>=', $to);
            });
        })->exists();
    }
}
