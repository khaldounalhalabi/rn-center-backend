<?php

namespace App\Services\v1\Formula;

use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\Terminals\Identifier;
use App\FormulaParser\EquationParser;
use App\Models\Formula;
use App\Repositories\FormulaRepository;
use App\Repositories\FormulaSegmentRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Formula>
 * @property FormulaRepository $repository
 */
class FormulaService extends BaseService
{
    use Makable;

    protected string $repositoryClass = FormulaRepository::class;

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $formula = $this->repository->find($id);

        if (!$formula) {
            return null;
        }

        if (isset($data['formula'])) {
            $expression = EquationParser::parse($data['formula']);
            $variables = $expression->getVariables();
            $data['template'] = $this->getFormulaHtmlFromExpression($expression, $data['formula']);
        }

        /** @var Formula $formula */
        $formula = parent::update($data, $formula);

        if (!$formula) {
            return null;
        }

        if (isset($data['segments']) && count($data['segments'])) {
            $formula->formulaSegments()->delete();
            $this->createFormulaSegments($data['segments'], $formula);
        }

        if (isset($variables)) {
            $this->storeUpdateFormulaVariables($variables, $formula);
        }

        return $formula->load($relationships)->loadCount($countable);
    }

    /**
     * @param              $segmentsData
     * @param Formula|null $formula
     * @return void
     */
    private function createFormulaSegments($segmentsData, ?Formula $formula): void
    {
        $segments = [];

        if (!count($segmentsData)) {
            foreach ($formula->splitSegments() as $segment) {
                $segments[] = [
                    'name' => "",
                    'segment' => $segment,
                    'formula_id' => $formula->id,
                ];
            }
        } else {
            foreach ($segmentsData as $sd) {
                $segments[] = [
                    'name' => $sd['name'] ?? "",
                    'segment' => $sd['segment'] ?? "",
                    'formula_id' => $formula->id,
                ];
            }
        }

        FormulaSegmentRepository::make()->insert($segments);
    }

    /**
     * @param Identifier[] $variables
     * @param Formula      $formula
     * @return void
     */
    private function storeUpdateFormulaVariables(array $variables, Formula $formula): void
    {
        $formulaVariablesIds = [];

        foreach ($variables as $variable) {
            $formulaVariablesIds[] = $variable->variable->id;
        }
        $formula->formulaVariables()->sync($formulaVariablesIds);
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $expression = EquationParser::parse($data['formula']);
        $variables = $expression->getVariables();
        $data['template'] = $this->getFormulaHtmlFromExpression($expression, $data['formula']);

        /** @var Formula $formula */
        $formula = parent::store($data);
        $this->createFormulaSegments($data['segments'] ?? [], $formula);

        $this->storeUpdateFormulaVariables($variables, $formula);

        return $formula->load($relationships)->loadCount($countable);
    }

    public function getFormulaHtmlFromExpression(Expression $expression, string $formula): array|string
    {
        foreach ($expression->getVariables() as $variable) {
            $formula = str_replace(
                $variable->variable->slug,
                "<var attr-description='{$variable->variable->description}' attr-label='{$variable?->variable?->name}'>{$variable->variable->slug}</var>",
                $formula
            );
        }

        return $formula;
    }
}
