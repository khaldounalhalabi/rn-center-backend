<?php

namespace Database\Factories;

use App\FormulaParser\EquationParser;
use App\Models\Formula;
use App\Models\FormulaSegment;
use App\Models\FormulaVariable;
use App\Models\Payslip;
use App\Models\User;
use App\Services\v1\Formula\FormulaService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends Factory
 */
class FormulaFactory extends Factory
{
    /** @var Collection<FormulaVariable> */
    private Collection $variables;

    const signs = [
        "*",
        "/",
        "+",
        "-",
    ];

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null, ?Collection $recycle = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection, $recycle);
        $variables = FormulaVariable::inRandomOrder()->take(3)->get();
        $this->variables = $variables;
    }

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $formula = $this->variables->get(0)->slug . fake()->randomElement(self::signs) . $this->variables->get(1)->slug . fake()->randomElement(self::signs) . $this->variables->get(2)->slug . fake()->randomElement(self::signs) . fake()->randomNumber(2);
        $template = FormulaService::make()->getFormulaHtmlFromExpression(EquationParser::parse($formula), $formula);
        return [
            'name' => fake()->firstName(),
            'formula' => $formula,
            'template' => $template,
        ];
    }

    public function withUsers($count = 1): FormulaFactory
    {
        return $this->has(User::factory($count));
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Formula $formula) {
            $segments = $formula->splitSegments();
            foreach ($segments as $segment) {
                FormulaSegment::create([
                    'segment' => $segment,
                    'name' => fake()->word(),
                    'formula_id' => $formula->id,
                ]);
            }

            $formula->formulaVariables()->sync($this->variables->pluck('id')->toArray());
        });
    }

    public function withFormulaSegments($count = 1): FormulaFactory
    {
        return $this->has(FormulaSegment::factory($count));
    }

    public function withFormulaVariables($count = 1): FormulaFactory
    {
        return $this->has(FormulaVariable::factory($count));
    }

    public function withPayslips($count = 1)
    {
        return $this->has(Payslip::factory($count));
    }
}
