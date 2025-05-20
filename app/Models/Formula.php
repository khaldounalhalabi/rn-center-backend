<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string name
 * @property string formula
 * @property string slug
 * @property string template
 */
class Formula extends Model
{
    use HasFactory;
    use Sluggable;

    protected $fillable = [
        'name',
        'formula',
        'slug',
        'template',
    ];

    protected $casts = [];

    public function exportable(): array
    {
        return [
            'name',
            'formula',
            'slug',
            'template',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [

            //filesKeys
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'formula',
            'slug',
            'template',

        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [];
    }

    public function sluggable(): array
    {
        return [
            [
                'col' => 'name',
                'slug_col' => 'slug',
                'separator' => '-',
            ],
        ];
    }

    public function formulaSegments(): HasMany
    {
        return $this->hasMany(FormulaSegment::class);
    }

    public function splitSegments(): array
    {
        $formula = $this->formula;
        $result = [];
        $buffer = [];
        $depth = 0;
        for ($i = 0; $i < strlen($formula); $i++) {
            $char = $formula[$i];
            if ($char === '(') {
                $depth++;
                $buffer[] = $char;
            } elseif ($char === ')') {
                $depth--;
                $buffer[] = $char;
            } elseif (($char === '+' || $char === '-') && $depth === 0) {
                $result[] = trim(implode('', $buffer));
                $buffer = [$char];
            } else {
                $buffer[] = $char;
            }
        }
        if (! empty($buffer)) {
            $result[] = trim(implode('', $buffer));
        }

        return array_map(function ($e) {
            return str_replace(' ', '', $e);
        }, $result);
    }

    public function formulaVariables(): BelongsToMany
    {
        return $this->belongsToMany(FormulaVariable::class, 'formula_formula_variables');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
