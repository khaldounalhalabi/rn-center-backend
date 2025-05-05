<?php

namespace App\FormulaParser\AntlrParser;
use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;

/**
 * This interface defines a complete listener for a parse tree produced by
 * {@see FormulaParser}.
 */
interface FormulaListener extends ParseTreeListener {
	/**
	 * Enter a parse tree produced by the `PositiveInt`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterPositiveInt(Context\PositiveIntContext $context): void;
	/**
	 * Exit a parse tree produced by the `PositiveInt` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitPositiveInt(Context\PositiveIntContext $context): void;
	/**
	 * Enter a parse tree produced by the `Multiplication`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterMultiplication(Context\MultiplicationContext $context): void;
	/**
	 * Exit a parse tree produced by the `Multiplication` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitMultiplication(Context\MultiplicationContext $context): void;
	/**
	 * Enter a parse tree produced by the `Addition`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterAddition(Context\AdditionContext $context): void;
	/**
	 * Exit a parse tree produced by the `Addition` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitAddition(Context\AdditionContext $context): void;
	/**
	 * Enter a parse tree produced by the `PositiveDouble`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterPositiveDouble(Context\PositiveDoubleContext $context): void;
	/**
	 * Exit a parse tree produced by the `PositiveDouble` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitPositiveDouble(Context\PositiveDoubleContext $context): void;
	/**
	 * Enter a parse tree produced by the `NegativeInt`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterNegativeInt(Context\NegativeIntContext $context): void;
	/**
	 * Exit a parse tree produced by the `NegativeInt` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitNegativeInt(Context\NegativeIntContext $context): void;
	/**
	 * Enter a parse tree produced by the `IFExpression`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterIFExpression(Context\IFExpressionContext $context): void;
	/**
	 * Exit a parse tree produced by the `IFExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitIFExpression(Context\IFExpressionContext $context): void;
	/**
	 * Enter a parse tree produced by the `Double`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterDouble(Context\DoubleContext $context): void;
	/**
	 * Exit a parse tree produced by the `Double` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitDouble(Context\DoubleContext $context): void;
	/**
	 * Enter a parse tree produced by the `Int`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterInt(Context\IntContext $context): void;
	/**
	 * Exit a parse tree produced by the `Int` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitInt(Context\IntContext $context): void;
	/**
	 * Enter a parse tree produced by the `PositiveId`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterPositiveId(Context\PositiveIdContext $context): void;
	/**
	 * Exit a parse tree produced by the `PositiveId` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitPositiveId(Context\PositiveIdContext $context): void;
	/**
	 * Enter a parse tree produced by the `Subtraction`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterSubtraction(Context\SubtractionContext $context): void;
	/**
	 * Exit a parse tree produced by the `Subtraction` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitSubtraction(Context\SubtractionContext $context): void;
	/**
	 * Enter a parse tree produced by the `PercentageOfPrevious`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterPercentageOfPrevious(Context\PercentageOfPreviousContext $context): void;
	/**
	 * Exit a parse tree produced by the `PercentageOfPrevious` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitPercentageOfPrevious(Context\PercentageOfPreviousContext $context): void;
	/**
	 * Enter a parse tree produced by the `NegativeId`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterNegativeId(Context\NegativeIdContext $context): void;
	/**
	 * Exit a parse tree produced by the `NegativeId` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitNegativeId(Context\NegativeIdContext $context): void;
	/**
	 * Enter a parse tree produced by the `BracedExpression`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterBracedExpression(Context\BracedExpressionContext $context): void;
	/**
	 * Exit a parse tree produced by the `BracedExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitBracedExpression(Context\BracedExpressionContext $context): void;
	/**
	 * Enter a parse tree produced by the `Division`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterDivision(Context\DivisionContext $context): void;
	/**
	 * Exit a parse tree produced by the `Division` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitDivision(Context\DivisionContext $context): void;
	/**
	 * Enter a parse tree produced by the `PositiveExpression`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterPositiveExpression(Context\PositiveExpressionContext $context): void;
	/**
	 * Exit a parse tree produced by the `PositiveExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitPositiveExpression(Context\PositiveExpressionContext $context): void;
	/**
	 * Enter a parse tree produced by the `Id`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterId(Context\IdContext $context): void;
	/**
	 * Exit a parse tree produced by the `Id` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitId(Context\IdContext $context): void;
	/**
	 * Enter a parse tree produced by the `PercentageOperation`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterPercentageOperation(Context\PercentageOperationContext $context): void;
	/**
	 * Exit a parse tree produced by the `PercentageOperation` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitPercentageOperation(Context\PercentageOperationContext $context): void;
	/**
	 * Enter a parse tree produced by the `NegativeDouble`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterNegativeDouble(Context\NegativeDoubleContext $context): void;
	/**
	 * Exit a parse tree produced by the `NegativeDouble` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitNegativeDouble(Context\NegativeDoubleContext $context): void;
	/**
	 * Enter a parse tree produced by the `NegativeExpression`
	 * labeled alternative in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterNegativeExpression(Context\NegativeExpressionContext $context): void;
	/**
	 * Exit a parse tree produced by the `NegativeExpression` labeled alternative
	 * in {@see FormulaParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitNegativeExpression(Context\NegativeExpressionContext $context): void;
	/**
	 * Enter a parse tree produced by the `LessThan`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterLessThan(Context\LessThanContext $context): void;
	/**
	 * Exit a parse tree produced by the `LessThan` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitLessThan(Context\LessThanContext $context): void;
	/**
	 * Enter a parse tree produced by the `LessThanOrEqual`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterLessThanOrEqual(Context\LessThanOrEqualContext $context): void;
	/**
	 * Exit a parse tree produced by the `LessThanOrEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitLessThanOrEqual(Context\LessThanOrEqualContext $context): void;
	/**
	 * Enter a parse tree produced by the `MoreThan`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterMoreThan(Context\MoreThanContext $context): void;
	/**
	 * Exit a parse tree produced by the `MoreThan` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitMoreThan(Context\MoreThanContext $context): void;
	/**
	 * Enter a parse tree produced by the `MoreThanOrEqual`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterMoreThanOrEqual(Context\MoreThanOrEqualContext $context): void;
	/**
	 * Exit a parse tree produced by the `MoreThanOrEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitMoreThanOrEqual(Context\MoreThanOrEqualContext $context): void;
	/**
	 * Enter a parse tree produced by the `NotEqual`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterNotEqual(Context\NotEqualContext $context): void;
	/**
	 * Exit a parse tree produced by the `NotEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitNotEqual(Context\NotEqualContext $context): void;
	/**
	 * Enter a parse tree produced by the `IsEqual`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterIsEqual(Context\IsEqualContext $context): void;
	/**
	 * Exit a parse tree produced by the `IsEqual` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitIsEqual(Context\IsEqualContext $context): void;
	/**
	 * Enter a parse tree produced by the `NotFunction`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterNotFunction(Context\NotFunctionContext $context): void;
	/**
	 * Exit a parse tree produced by the `NotFunction` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitNotFunction(Context\NotFunctionContext $context): void;
	/**
	 * Enter a parse tree produced by the `AndFunction`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterAndFunction(Context\AndFunctionContext $context): void;
	/**
	 * Exit a parse tree produced by the `AndFunction` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitAndFunction(Context\AndFunctionContext $context): void;
	/**
	 * Enter a parse tree produced by the `OrFunction`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterOrFunction(Context\OrFunctionContext $context): void;
	/**
	 * Exit a parse tree produced by the `OrFunction` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitOrFunction(Context\OrFunctionContext $context): void;
	/**
	 * Enter a parse tree produced by the `BracedBooleanOperation`
	 * labeled alternative in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function enterBracedBooleanOperation(Context\BracedBooleanOperationContext $context): void;
	/**
	 * Exit a parse tree produced by the `BracedBooleanOperation` labeled alternative
	 * in {@see FormulaParser::booleanOperations()}.
	 * @param $context The parse tree.
	 */
	public function exitBracedBooleanOperation(Context\BracedBooleanOperationContext $context): void;
}
