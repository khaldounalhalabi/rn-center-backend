<?php

namespace App\FormulaParser\AntlrParser;

use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;

/**
 * This interface defines a complete generic visitor for a parse tree produced by {@see FormulaParser}.
 */
interface FormulaVisitor extends ParseTreeVisitor
{
	/**
	 * Visit a parse tree produced by {@see FormulaParser::formula()}.
	 *
	 * @param Context\FormulaContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFormula(Context\FormulaContext $context);

	/**
	 * Visit a parse tree produced by the `PositiveInt` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\PositiveIntContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPositiveInt(Context\PositiveIntContext $context);

	/**
	 * Visit a parse tree produced by the `PositiveIFExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\PositiveIFExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPositiveIFExpression(Context\PositiveIFExpressionContext $context);

	/**
	 * Visit a parse tree produced by the `PositiveDouble` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\PositiveDoubleContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPositiveDouble(Context\PositiveDoubleContext $context);

	/**
	 * Visit a parse tree produced by the `MulDiv` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\MulDivContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitMulDiv(Context\MulDivContext $context);

	/**
	 * Visit a parse tree produced by the `AddSub` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\AddSubContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAddSub(Context\AddSubContext $context);

	/**
	 * Visit a parse tree produced by the `NegativeInt` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\NegativeIntContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNegativeInt(Context\NegativeIntContext $context);

	/**
	 * Visit a parse tree produced by the `IFExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\IFExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIFExpression(Context\IFExpressionContext $context);

	/**
	 * Visit a parse tree produced by the `Double` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\DoubleContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDouble(Context\DoubleContext $context);

	/**
	 * Visit a parse tree produced by the `Int` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\IntContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitInt(Context\IntContext $context);

	/**
	 * Visit a parse tree produced by the `PositiveId` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\PositiveIdContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPositiveId(Context\PositiveIdContext $context);

	/**
	 * Visit a parse tree produced by the `NegativeIFExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\NegativeIFExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNegativeIFExpression(Context\NegativeIFExpressionContext $context);

	/**
	 * Visit a parse tree produced by the `PercentageOfPrevious` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\PercentageOfPreviousContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPercentageOfPrevious(Context\PercentageOfPreviousContext $context);

	/**
	 * Visit a parse tree produced by the `NegativeId` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\NegativeIdContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNegativeId(Context\NegativeIdContext $context);

	/**
	 * Visit a parse tree produced by the `BracedExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\BracedExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBracedExpression(Context\BracedExpressionContext $context);

	/**
	 * Visit a parse tree produced by the `PositiveExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\PositiveExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPositiveExpression(Context\PositiveExpressionContext $context);

	/**
	 * Visit a parse tree produced by the `Id` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\IdContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitId(Context\IdContext $context);

	/**
	 * Visit a parse tree produced by the `PercentageOperation` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\PercentageOperationContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPercentageOperation(Context\PercentageOperationContext $context);

	/**
	 * Visit a parse tree produced by the `NegativeDouble` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\NegativeDoubleContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNegativeDouble(Context\NegativeDoubleContext $context);

	/**
	 * Visit a parse tree produced by the `NegativeExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 *
	 * @param Context\NegativeExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNegativeExpression(Context\NegativeExpressionContext $context);

	/**
	 * Visit a parse tree produced by the `LessThan` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\LessThanContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLessThan(Context\LessThanContext $context);

	/**
	 * Visit a parse tree produced by the `LessThanOrEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\LessThanOrEqualContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLessThanOrEqual(Context\LessThanOrEqualContext $context);

	/**
	 * Visit a parse tree produced by the `MoreThan` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\MoreThanContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitMoreThan(Context\MoreThanContext $context);

	/**
	 * Visit a parse tree produced by the `MoreThanOrEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\MoreThanOrEqualContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitMoreThanOrEqual(Context\MoreThanOrEqualContext $context);

	/**
	 * Visit a parse tree produced by the `NotEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\NotEqualContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNotEqual(Context\NotEqualContext $context);

	/**
	 * Visit a parse tree produced by the `IsEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\IsEqualContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIsEqual(Context\IsEqualContext $context);

	/**
	 * Visit a parse tree produced by the `NotFunction` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\NotFunctionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNotFunction(Context\NotFunctionContext $context);

	/**
	 * Visit a parse tree produced by the `AndFunction` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\AndFunctionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAndFunction(Context\AndFunctionContext $context);

	/**
	 * Visit a parse tree produced by the `OrFunction` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\OrFunctionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitOrFunction(Context\OrFunctionContext $context);

	/**
	 * Visit a parse tree produced by the `BracedBooleanOperation` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 *
	 * @param Context\BracedBooleanOperationContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBracedBooleanOperation(Context\BracedBooleanOperationContext $context);
}
