<?php

namespace App\Models;

use App\Traits\HasFormulaString;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string  name
 * @property string  segment
 * @property int     formula_id
 * @property Formula formula
 */
class FormulaSegment extends Model
{
    use HasFactory;
    use HasFormulaString;

    protected $fillable = [
        'name',
        'segment',
        'formula_id',
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'name',
            'segment',
            'formula.name',

        ];
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'segment',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [

        ];
    }
}
