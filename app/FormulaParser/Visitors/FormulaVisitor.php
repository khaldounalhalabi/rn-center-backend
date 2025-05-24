<?php

namespace App\FormulaParser\Visitors;

use Antlr\Antlr4\Runtime\RuleContext;
use Antlr\Antlr4\Runtime\Token;
use Antlr\Antlr4\Runtime\Tree\ParseTree;
use App\Exceptions\FormulaSyntaxException;
use App\FormulaParser\AntlrParser\Context;
use App\FormulaParser\AntlrParser\Context\AddSubContext;
use App\FormulaParser\AntlrParser\Context\IdContext;
use App\FormulaParser\AntlrParser\Context\IsEqualContext;
use App\FormulaParser\AntlrParser\Context\LessThanContext;
use App\FormulaParser\AntlrParser\Context\LessThanOrEqualContext;
use App\FormulaParser\AntlrParser\Context\MoreThanContext;
use App\FormulaParser\AntlrParser\Context\MoreThanOrEqualContext;
use App\FormulaParser\AntlrParser\Context\MulDivContext;
use App\FormulaParser\AntlrParser\Context\NegativeIdContext;
use App\FormulaParser\AntlrParser\Context\NotEqualContext;
use App\FormulaParser\AntlrParser\Context\PercentageOperationContext;
use App\FormulaParser\AntlrParser\Context\PositiveIdContext;
use App\FormulaParser\AntlrParser\FormulaBaseVisitor;
use App\FormulaParser\Ast\ArithmeticExpressions\Addition;
use App\FormulaParser\Ast\ArithmeticExpressions\BracedExpression;
use App\FormulaParser\Ast\ArithmeticExpressions\Division;
use App\FormulaParser\Ast\ArithmeticExpressions\Multiplication;
use App\FormulaParser\Ast\ArithmeticExpressions\NegativeDouble;
use App\FormulaParser\Ast\ArithmeticExpressions\NegativeExpression;
use App\FormulaParser\Ast\ArithmeticExpressions\NegativeIdentifier;
use App\FormulaParser\Ast\ArithmeticExpressions\NegativeInteger;
use App\FormulaParser\Ast\ArithmeticExpressions\PercentageOfPrevious;
use App\FormulaParser\Ast\ArithmeticExpressions\PercentageOperation;
use App\FormulaParser\Ast\ArithmeticExpressions\Subtraction;
use App\FormulaParser\Ast\BooleanExpressions\BracedBooleanExpression;
use App\FormulaParser\Ast\BooleanExpressions\IsEqual;
use App\FormulaParser\Ast\BooleanExpressions\LessThan;
use App\FormulaParser\Ast\BooleanExpressions\LessThanOrEqual;
use App\FormulaParser\Ast\BooleanExpressions\MoreThan;
use App\FormulaParser\Ast\BooleanExpressions\MoreThanOrEqual;
use App\FormulaParser\Ast\BooleanExpressions\NotEqual;
use App\FormulaParser\Ast\Expression;
use App\FormulaParser\Ast\Functions\AndFunction;
use App\FormulaParser\Ast\Functions\NotFunction;
use App\FormulaParser\Ast\Functions\OrFunction;
use App\FormulaParser\Ast\IFExpression;
use App\FormulaParser\Ast\NegativeIFExpression;
use App\FormulaParser\Ast\PositiveIFExpression;
use App\FormulaParser\Ast\Terminals\Double;
use App\FormulaParser\Ast\Terminals\Double as ParserDouble;
use App\FormulaParser\Ast\Terminals\Identifier;
use App\FormulaParser\Ast\Terminals\Integer as ParserInteger;
use App\Models\FormulaVariable;
use Exception;
use Illuminate\Validation\ValidationException;

/**
 * @extends FormulaBaseVisitor<Expression>
 */
class FormulaVisitor extends FormulaBaseVisitor
{
    public function __construct()
    {
    }


    /**
     * @param PercentageOperationContext|MulDivContext|AddSubContext|LessThanContext|LessThanOrEqualContext|MoreThanContext|MoreThanOrEqualContext|IsEqualContext|NotEqualContext $context
     * @return Expression[]
     */
    private function getLeftAndRight(
        Context\PercentageOperationContext|Context\MulDivContext|Context\AddSubContext|LessThanContext|LessThanOrEqualContext|MoreThanContext|MoreThanOrEqualContext|IsEqualContext|NotEqualContext $context
    ): array
    {
        return [
            $this->visit($context->left),
            $this->visit($context->right),
        ];
    }

    /**
     * @param MulDivContext $context
     * @return Multiplication|Division
     * @throws Exception
     */
    public function visitMulDiv(Context\MulDivContext $context): Multiplication|Division
    {
        $left = $this->visit($context->left);
        $right = $this->visit($context->right);
        $op = $context->op->getText();

        if ($op == "*") {
            return new Multiplication($left, $right);
        } elseif ($op == "/") {
            return new Division($left, $right);
        }

        throw new Exception("Unknown operator inside a MulDiv visitor");
    }


    /**
     * @param AddSubContext $context
     * @return Addition|Subtraction
     * @throws Exception
     */
    public function visitAddSub(Context\AddSubContext $context): Addition|Subtraction
    {
        $left = $this->visit($context->left);
        $right = $this->visit($context->right);
        $op = $context->op->getText();

        if ($op == "+") {
            return new Addition($left, $right);
        } elseif ($op == "-") {
            return new Subtraction($left, $right);
        }

        throw new Exception("Unknown operator inside a MulDiv visitor");
    }

    /**
     * @param Context\PercentageOfPreviousContext $context
     * @return PercentageOfPrevious
     */
    public function visitPercentageOfPrevious(Context\PercentageOfPreviousContext $context): PercentageOfPrevious
    {
        $parent = $context->parentCtx;

        if (!$parent) {
            throw ValidationException::withMessages([
                'formula' => "Invalid Operation : {$context->getText()} of What ? ",
            ]);
        }

        for ($i = 0; $i <= $parent->getChildCount(); $i++) {
            $child = $parent->getChild($i);
            $adjacentChild = $parent->getChild($i + 2);

            if (
                $child instanceof Context\ExpressionContext
                && $this->areContextsEqual($adjacentChild, $context)
            ) {
                $prevExpression = $this->visit($child);
                break;
            }
        }

        if (!isset($prevExpression)) {
            throw ValidationException::withMessages([
                'formula' => "Invalid Operation : {$context->getText()} of What ? ",
            ]);
        }

        return new PercentageOfPrevious(
            $prevExpression,
            $this->visit($context->expression())
        );
    }

    /**
     * @param Context\BracedExpressionContext $context
     * @return BracedExpression
     */
    public function visitBracedExpression(Context\BracedExpressionContext $context): BracedExpression
    {
        return new BracedExpression($this->visit($context->expression()));
    }

    /**
     * @param Context\PercentageOperationContext $context
     * @return PercentageOperation
     */
    public function visitPercentageOperation(Context\PercentageOperationContext $context): PercentageOperation
    {
        [$left, $right] = $this->getLeftAndRight($context);

        return new PercentageOperation($left, $right);
    }

    /**
     * @param Context\IntContext $context
     * @return ParserInteger
     */
    public function visitInt(Context\IntContext $context): ParserInteger
    {
        return new ParserInteger($context->getText());
    }

    /**
     * @param Context\DoubleContext $context
     * @return ParserDouble
     */
    public function visitDouble(Context\DoubleContext $context): ParserDouble
    {
        return new ParserDouble($context->getText());
    }

    /**
     * @param ParseTree|null $context1
     * @param ParseTree|null $context2
     * @return bool
     */
    private function areContextsEqual(?ParseTree $context1, ?ParseTree $context2): bool
    {
        if (!$context1 || !$context2) {
            return false;
        }

        $token1 = $context1->getPayload();
        $token2 = $context2->getPayload();

        if (get_class($token2) != get_class($token1)) {
            return false;
        }

        if ($token1->getText() === $token2->getText()) {
            if ($token1 instanceof Token) {
                return $token1->getType() === $token2->getType() && $token1->getText() === $token2->getText();
            } elseif ($token1 instanceof RuleContext) {
                return $token1->getRuleIndex() === $token2->getRuleIndex();
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @param Context\IdContext $context
     * @return Identifier
     * @throws FormulaSyntaxException
     */
    public function visitId(Context\IdContext $context): Identifier
    {
        $idName = $context->getText();
        $variable = $this->getVariableModel($idName, $context);

        return new Identifier(
            $idName,
            $variable,
            $context->start->getStartIndex(),
            $context->stop->getStopIndex()
        );
    }

    public function visitNegativeDouble(Context\NegativeDoubleContext $context): NegativeDouble
    {
        $number = $context->Double()->getText();
        return new NegativeDouble($number);
    }

    /**
     * @throws FormulaSyntaxException
     */
    public function visitNegativeId(Context\NegativeIdContext $context): NegativeIdentifier
    {
        $idName = $context->Variable()->getText();
        $variable = $this->getVariableModel($idName, $context);

        $identifier = new Identifier(
            $idName,
            $variable,
            $context->Variable()->getSymbol()->getStartIndex(),
            $context->Variable()->getSymbol()->getStopIndex()
        );

        return new NegativeIdentifier($identifier);
    }

    public function visitNegativeInt(Context\NegativeIntContext $context): NegativeInteger
    {
        $number = $context->IntegerLiteral()->getText();
        return new NegativeInteger($number);
    }

    public function visitNegativeExpression(Context\NegativeExpressionContext $context): NegativeExpression
    {
        $expression = $context->expression();
        return new NegativeExpression($this->visit($expression));
    }

    public function visitPositiveDouble(Context\PositiveDoubleContext $context): ParserDouble
    {
        $number = $context->Double()->getText();
        return new Double($number);
    }

    public function visitPositiveInt(Context\PositiveIntContext $context): ParserInteger
    {
        $number = $context->IntegerLiteral()->getText();
        return new ParserInteger($number);
    }

    /**
     * @param PositiveIdContext $context
     * @return Identifier
     * @throws FormulaSyntaxException
     */
    public function visitPositiveId(Context\PositiveIdContext $context): Identifier
    {
        $idName = $context->Variable()->getText();
        $variable = $this->getVariableModel(
            $idName,
            $context
        );
        return new Identifier(
            $idName,
            $variable,
            $context->Variable()->getSymbol()->getStartIndex(),
            $context->Variable()->getSymbol()->getStopIndex()
        );
    }

    public function visitPositiveExpression(Context\PositiveExpressionContext $context)
    {
        $expression = $context->expression();
        return $this->visit($expression);
    }


    /**
     * @param string|null                                   $idName
     * @param NegativeIdContext|PositiveIdContext|IdContext $context
     * @return FormulaVariable
     * @throws FormulaSyntaxException
     */
    private function getVariableModel(?string $idName, Context\NegativeIdContext|Context\PositiveIdContext|Context\IdContext $context): FormulaVariable
    {
        $variable = FormulaVariable::where('slug', $idName)->first();

        if (!$variable) {
            throw new FormulaSyntaxException(
                "Undefined Variable [$idName] in line : {$context->start->getLine()} in column : {$context->start->getCharPositionInLine()}",
                $context->start->getLine(),
                $context->start->getCharPositionInLine()
            );
        }
        return $variable;
    }


    public function visitLessThan(Context\LessThanContext $context): LessThan
    {
        [$left, $right] = $this->getLeftAndRight($context);

        return new LessThan($left, $right);
    }

    public function visitLessThanOrEqual(Context\LessThanOrEqualContext $context): LessThanOrEqual
    {
        [$left, $right] = $this->getLeftAndRight($context);

        return new LessThanOrEqual($left, $right);
    }

    public function visitMoreThan(Context\MoreThanContext $context): MoreThan
    {
        [$left, $right] = $this->getLeftAndRight($context);

        return new MoreThan($left, $right);
    }

    public function visitMoreThanOrEqual(Context\MoreThanOrEqualContext $context): MoreThanOrEqual
    {
        [$left, $right] = $this->getLeftAndRight($context);

        return new MoreThanOrEqual($left, $right);
    }

    public function visitNotEqual(Context\NotEqualContext $context): NotEqual
    {
        [$left, $right] = $this->getLeftAndRight($context);

        return new NotEqual($left, $right);
    }

    public function visitIsEqual(Context\IsEqualContext $context): IsEqual
    {
        [$left, $right] = $this->getLeftAndRight($context);

        return new IsEqual($left, $right);
    }

    public function visitNotFunction(Context\NotFunctionContext $context): NotFunction
    {
        $booleanExpressions = array_map(fn($exp) => $this->visit($exp), $context->booleanOperations());

        return new NotFunction($booleanExpressions);
    }

    public function visitAndFunction(Context\AndFunctionContext $context): AndFunction
    {
        $booleanExpressions = array_map(fn($exp) => $this->visit($exp), $context->booleanOperations());

        return new AndFunction($booleanExpressions);
    }

    public function visitOrFunction(Context\OrFunctionContext $context): OrFunction
    {
        $booleanExpressions = array_map(fn($exp) => $this->visit($exp), $context->booleanOperations());

        return new OrFunction($booleanExpressions);
    }

    public function visitIFExpression(Context\IFExpressionContext $context): IFExpression
    {
        $condition = $this->visit($context->booleanOperations());
        $then = $this->visit($context->expression(0));
        $else = $this->visit($context->expression(1));
        return new IFExpression($condition, $then, $else);
    }

    public function visitNegativeIFExpression(Context\NegativeIFExpressionContext $context): NegativeIFExpression
    {
        $condition = $this->visit($context->booleanOperations());
        $then = $this->visit($context->expression(0));
        $else = $this->visit($context->expression(1));
        return new NegativeIFExpression($condition, $then, $else);
    }

    public function visitPositiveIFExpression(Context\PositiveIFExpressionContext $context): PositiveIFExpression
    {
        $condition = $this->visit($context->booleanOperations());
        $then = $this->visit($context->expression(0));
        $else = $this->visit($context->expression(1));
        return new PositiveIFExpression($condition, $then, $else);
    }

    public function visitBracedBooleanOperation(Context\BracedBooleanOperationContext $context): BracedBooleanExpression
    {
        return new BracedBooleanExpression($this->visit($context->booleanOperations()));
    }

    public function visitFormula(Context\FormulaContext $context)
    {
        return $this->visit($context->expression());
    }
}
