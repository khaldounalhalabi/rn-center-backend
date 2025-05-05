<?php

namespace App\FormulaParser\AntlrParser {
	use Antlr\Antlr4\Runtime\Atn\ATN;
	use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
	use Antlr\Antlr4\Runtime\Atn\ParserATNSimulator;
	use Antlr\Antlr4\Runtime\Dfa\DFA;
	use Antlr\Antlr4\Runtime\Error\Exceptions\FailedPredicateException;
	use Antlr\Antlr4\Runtime\Error\Exceptions\NoViableAltException;
	use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
	use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;
	use Antlr\Antlr4\Runtime\RuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\TokenStream;
	use Antlr\Antlr4\Runtime\Vocabulary;
	use Antlr\Antlr4\Runtime\VocabularyImpl;
	use Antlr\Antlr4\Runtime\RuntimeMetaData;
	use Antlr\Antlr4\Runtime\Parser;

	final class FormulaParser extends Parser
	{
		public const T__0 = 1, T__1 = 2, T__2 = 3, T__3 = 4, T__4 = 5, T__5 = 6,
               T__6 = 7, T__7 = 8, T__8 = 9, T__9 = 10, T__10 = 11, T__11 = 12,
               T__12 = 13, T__13 = 14, T__14 = 15, T__15 = 16, T__16 = 17,
               T__17 = 18, T__18 = 19, Variable = 20, IntegerLiteral = 21,
               Double = 22, WS = 23;

		public const RULE_formula = 0, RULE_expression = 1, RULE_booleanOperations = 2;

		/**
		 * @var array<string>
		 */
		public const RULE_NAMES = [
			'formula', 'expression', 'booleanOperations'
		];

		/**
		 * @var array<string|null>
		 */
		private const LITERAL_NAMES = [
		    null, "'('", "')'", "'%'", "'*'", "'/'", "'+'", "'-'", "'IF'", "','",
		    "'<'", "'<='", "'>'", "'>='", "'!='", "'<>'", "'='", "'NOT'", "'AND'",
		    "'OR'"
		];

		/**
		 * @var array<string>
		 */
		private const SYMBOLIC_NAMES = [
		    null, null, null, null, null, null, null, null, null, null, null,
		    null, null, null, null, null, null, null, null, null, "Variable",
		    "IntegerLiteral", "Double", "WS"
		];

		private const SERIALIZED_ATN =
			[4, 1, 23, 133, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 1, 0, 1, 0, 1, 0,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 3, 1, 49, 8, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 5, 1, 62, 8, 1,
		    10, 1, 12, 1, 65, 9, 1, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2,
		    1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2,
		    1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2,
		    5, 2, 96, 8, 2, 10, 2, 12, 2, 99, 9, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1,
		    2, 1, 2, 1, 2, 5, 2, 108, 8, 2, 10, 2, 12, 2, 111, 9, 2, 1, 2, 1,
		    2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 5, 2, 120, 8, 2, 10, 2, 12, 2, 123,
		    9, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 3, 2, 131, 8, 2, 1, 2, 0,
		    1, 2, 3, 0, 2, 4, 0, 3, 1, 0, 4, 5, 1, 0, 6, 7, 1, 0, 14, 15, 157,
		    0, 6, 1, 0, 0, 0, 2, 48, 1, 0, 0, 0, 4, 130, 1, 0, 0, 0, 6, 7, 3,
		    2, 1, 0, 7, 8, 5, 0, 0, 1, 8, 1, 1, 0, 0, 0, 9, 10, 6, 1, -1, 0, 10,
		    11, 5, 1, 0, 0, 11, 12, 3, 2, 1, 0, 12, 13, 5, 2, 0, 0, 13, 49, 1,
		    0, 0, 0, 14, 15, 5, 7, 0, 0, 15, 16, 5, 1, 0, 0, 16, 17, 3, 2, 1,
		    0, 17, 18, 5, 2, 0, 0, 18, 49, 1, 0, 0, 0, 19, 20, 5, 6, 0, 0, 20,
		    21, 5, 1, 0, 0, 21, 22, 3, 2, 1, 0, 22, 23, 5, 2, 0, 0, 23, 49, 1,
		    0, 0, 0, 24, 25, 5, 8, 0, 0, 25, 26, 5, 1, 0, 0, 26, 27, 3, 4, 2,
		    0, 27, 28, 5, 9, 0, 0, 28, 29, 3, 2, 1, 0, 29, 30, 5, 9, 0, 0, 30,
		    31, 3, 2, 1, 0, 31, 32, 5, 2, 0, 0, 32, 49, 1, 0, 0, 0, 33, 49, 5,
		    20, 0, 0, 34, 49, 5, 21, 0, 0, 35, 49, 5, 22, 0, 0, 36, 37, 5, 7,
		    0, 0, 37, 49, 5, 20, 0, 0, 38, 39, 5, 7, 0, 0, 39, 49, 5, 21, 0, 0,
		    40, 41, 5, 7, 0, 0, 41, 49, 5, 22, 0, 0, 42, 43, 5, 6, 0, 0, 43, 49,
		    5, 20, 0, 0, 44, 45, 5, 6, 0, 0, 45, 49, 5, 21, 0, 0, 46, 47, 5, 6,
		    0, 0, 47, 49, 5, 22, 0, 0, 48, 9, 1, 0, 0, 0, 48, 14, 1, 0, 0, 0,
		    48, 19, 1, 0, 0, 0, 48, 24, 1, 0, 0, 0, 48, 33, 1, 0, 0, 0, 48, 34,
		    1, 0, 0, 0, 48, 35, 1, 0, 0, 0, 48, 36, 1, 0, 0, 0, 48, 38, 1, 0,
		    0, 0, 48, 40, 1, 0, 0, 0, 48, 42, 1, 0, 0, 0, 48, 44, 1, 0, 0, 0,
		    48, 46, 1, 0, 0, 0, 49, 63, 1, 0, 0, 0, 50, 51, 10, 16, 0, 0, 51,
		    52, 5, 3, 0, 0, 52, 62, 3, 2, 1, 17, 53, 54, 10, 14, 0, 0, 54, 55,
		    7, 0, 0, 0, 55, 62, 3, 2, 1, 15, 56, 57, 10, 13, 0, 0, 57, 58, 7,
		    1, 0, 0, 58, 62, 3, 2, 1, 14, 59, 60, 10, 15, 0, 0, 60, 62, 5, 3,
		    0, 0, 61, 50, 1, 0, 0, 0, 61, 53, 1, 0, 0, 0, 61, 56, 1, 0, 0, 0,
		    61, 59, 1, 0, 0, 0, 62, 65, 1, 0, 0, 0, 63, 61, 1, 0, 0, 0, 63, 64,
		    1, 0, 0, 0, 64, 3, 1, 0, 0, 0, 65, 63, 1, 0, 0, 0, 66, 67, 3, 2, 1,
		    0, 67, 68, 5, 10, 0, 0, 68, 69, 3, 2, 1, 0, 69, 131, 1, 0, 0, 0, 70,
		    71, 3, 2, 1, 0, 71, 72, 5, 11, 0, 0, 72, 73, 3, 2, 1, 0, 73, 131,
		    1, 0, 0, 0, 74, 75, 3, 2, 1, 0, 75, 76, 5, 12, 0, 0, 76, 77, 3, 2,
		    1, 0, 77, 131, 1, 0, 0, 0, 78, 79, 3, 2, 1, 0, 79, 80, 5, 13, 0, 0,
		    80, 81, 3, 2, 1, 0, 81, 131, 1, 0, 0, 0, 82, 83, 3, 2, 1, 0, 83, 84,
		    7, 2, 0, 0, 84, 85, 3, 2, 1, 0, 85, 131, 1, 0, 0, 0, 86, 87, 3, 2,
		    1, 0, 87, 88, 5, 16, 0, 0, 88, 89, 3, 2, 1, 0, 89, 131, 1, 0, 0, 0,
		    90, 91, 5, 17, 0, 0, 91, 92, 5, 1, 0, 0, 92, 97, 3, 4, 2, 0, 93, 94,
		    5, 9, 0, 0, 94, 96, 3, 4, 2, 0, 95, 93, 1, 0, 0, 0, 96, 99, 1, 0,
		    0, 0, 97, 95, 1, 0, 0, 0, 97, 98, 1, 0, 0, 0, 98, 100, 1, 0, 0, 0,
		    99, 97, 1, 0, 0, 0, 100, 101, 5, 2, 0, 0, 101, 131, 1, 0, 0, 0, 102,
		    103, 5, 18, 0, 0, 103, 104, 5, 1, 0, 0, 104, 109, 3, 4, 2, 0, 105,
		    106, 5, 9, 0, 0, 106, 108, 3, 4, 2, 0, 107, 105, 1, 0, 0, 0, 108,
		    111, 1, 0, 0, 0, 109, 107, 1, 0, 0, 0, 109, 110, 1, 0, 0, 0, 110,
		    112, 1, 0, 0, 0, 111, 109, 1, 0, 0, 0, 112, 113, 5, 2, 0, 0, 113,
		    131, 1, 0, 0, 0, 114, 115, 5, 19, 0, 0, 115, 116, 5, 1, 0, 0, 116,
		    121, 3, 4, 2, 0, 117, 118, 5, 9, 0, 0, 118, 120, 3, 4, 2, 0, 119,
		    117, 1, 0, 0, 0, 120, 123, 1, 0, 0, 0, 121, 119, 1, 0, 0, 0, 121,
		    122, 1, 0, 0, 0, 122, 124, 1, 0, 0, 0, 123, 121, 1, 0, 0, 0, 124,
		    125, 5, 2, 0, 0, 125, 131, 1, 0, 0, 0, 126, 127, 5, 1, 0, 0, 127,
		    128, 3, 4, 2, 0, 128, 129, 5, 2, 0, 0, 129, 131, 1, 0, 0, 0, 130,
		    66, 1, 0, 0, 0, 130, 70, 1, 0, 0, 0, 130, 74, 1, 0, 0, 0, 130, 78,
		    1, 0, 0, 0, 130, 82, 1, 0, 0, 0, 130, 86, 1, 0, 0, 0, 130, 90, 1,
		    0, 0, 0, 130, 102, 1, 0, 0, 0, 130, 114, 1, 0, 0, 0, 130, 126, 1,
		    0, 0, 0, 131, 5, 1, 0, 0, 0, 7, 48, 61, 63, 97, 109, 121, 130];
		protected static $atn;
		protected static $decisionToDFA;
		protected static $sharedContextCache;

		public function __construct(TokenStream $input)
		{
			parent::__construct($input);

			self::initialize();

			$this->interp = new ParserATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
		}

		private static function initialize(): void
		{
			if (self::$atn !== null) {
				return;
			}

			RuntimeMetaData::checkVersion('4.13.2', RuntimeMetaData::VERSION);

			$atn = (new ATNDeserializer())->deserialize(self::SERIALIZED_ATN);

			$decisionToDFA = [];
			for ($i = 0, $count = $atn->getNumberOfDecisions(); $i < $count; $i++) {
				$decisionToDFA[] = new DFA($atn->getDecisionState($i), $i);
			}

			self::$atn = $atn;
			self::$decisionToDFA = $decisionToDFA;
			self::$sharedContextCache = new PredictionContextCache();
		}

		public function getGrammarFileName(): string
		{
			return "Formula.g4";
		}

		public function getRuleNames(): array
		{
			return self::RULE_NAMES;
		}

		public function getSerializedATN(): array
		{
			return self::SERIALIZED_ATN;
		}

		public function getATN(): ATN
		{
			return self::$atn;
		}

		public function getVocabulary(): Vocabulary
        {
            static $vocabulary;

			return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
        }

		/**
		 * @throws RecognitionException
		 */
		public function formula(): Context\FormulaContext
		{
		    $localContext = new Context\FormulaContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 0, self::RULE_formula);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(6);
		        $this->recursiveExpression(0);
		        $this->setState(7);
		        $this->match(self::EOF);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function expression(): Context\ExpressionContext
		{
			return $this->recursiveExpression(0);
		}

		/**
		 * @throws RecognitionException
		 */
		private function recursiveExpression(int $precedence): Context\ExpressionContext
		{
			$parentContext = $this->ctx;
			$parentState = $this->getState();
			$localContext = new Context\ExpressionContext($this->ctx, $parentState);
			$previousContext = $localContext;
			$startState = 2;
			$this->enterRecursionRule($localContext, 2, self::RULE_expression, $precedence);

			try {
				$this->enterOuterAlt($localContext, 1);
				$this->setState(48);
				$this->errorHandler->sync($this);

				switch ($this->getInterpreter()->adaptivePredict($this->input, 0, $this->ctx)) {
					case 1:
					    $localContext = new Context\BracedExpressionContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;

					    $this->setState(10);
					    $this->match(self::T__0);
					    $this->setState(11);
					    $this->recursiveExpression(0);
					    $this->setState(12);
					    $this->match(self::T__1);
					break;

					case 2:
					    $localContext = new Context\NegativeExpressionContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(14);
					    $this->match(self::T__6);
					    $this->setState(15);
					    $this->match(self::T__0);
					    $this->setState(16);
					    $this->recursiveExpression(0);
					    $this->setState(17);
					    $this->match(self::T__1);
					break;

					case 3:
					    $localContext = new Context\PositiveExpressionContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(19);
					    $this->match(self::T__5);
					    $this->setState(20);
					    $this->match(self::T__0);
					    $this->setState(21);
					    $this->recursiveExpression(0);
					    $this->setState(22);
					    $this->match(self::T__1);
					break;

					case 4:
					    $localContext = new Context\IFExpressionContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(24);
					    $this->match(self::T__7);
					    $this->setState(25);
					    $this->match(self::T__0);
					    $this->setState(26);
					    $localContext->condition = $this->booleanOperations();
					    $this->setState(27);
					    $this->match(self::T__8);
					    $this->setState(28);
					    $localContext->then = $this->recursiveExpression(0);
					    $this->setState(29);
					    $this->match(self::T__8);
					    $this->setState(30);
					    $localContext->else = $this->recursiveExpression(0);
					    $this->setState(31);
					    $this->match(self::T__1);
					break;

					case 5:
					    $localContext = new Context\IdContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(33);
					    $this->match(self::Variable);
					break;

					case 6:
					    $localContext = new Context\IntContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(34);
					    $this->match(self::IntegerLiteral);
					break;

					case 7:
					    $localContext = new Context\DoubleContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(35);
					    $this->match(self::Double);
					break;

					case 8:
					    $localContext = new Context\NegativeIdContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(36);
					    $this->match(self::T__6);
					    $this->setState(37);
					    $this->match(self::Variable);
					break;

					case 9:
					    $localContext = new Context\NegativeIntContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(38);
					    $this->match(self::T__6);
					    $this->setState(39);
					    $this->match(self::IntegerLiteral);
					break;

					case 10:
					    $localContext = new Context\NegativeDoubleContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(40);
					    $this->match(self::T__6);
					    $this->setState(41);
					    $this->match(self::Double);
					break;

					case 11:
					    $localContext = new Context\PositiveIdContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(42);
					    $this->match(self::T__5);
					    $this->setState(43);
					    $this->match(self::Variable);
					break;

					case 12:
					    $localContext = new Context\PositiveIntContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(44);
					    $this->match(self::T__5);
					    $this->setState(45);
					    $this->match(self::IntegerLiteral);
					break;

					case 13:
					    $localContext = new Context\PositiveDoubleContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(46);
					    $this->match(self::T__5);
					    $this->setState(47);
					    $this->match(self::Double);
					break;
				}
				$this->ctx->stop = $this->input->LT(-1);
				$this->setState(63);
				$this->errorHandler->sync($this);

				$alt = $this->getInterpreter()->adaptivePredict($this->input, 2, $this->ctx);

				while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
					if ($alt === 1) {
						if ($this->getParseListeners() !== null) {
						    $this->triggerExitRuleEvent();
						}

						$previousContext = $localContext;
						$this->setState(61);
						$this->errorHandler->sync($this);

						switch ($this->getInterpreter()->adaptivePredict($this->input, 1, $this->ctx)) {
							case 1:
							    $localContext = new Context\PercentageOperationContext(new Context\ExpressionContext($parentContext, $parentState));
							    $localContext->left = $previousContext;

							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(50);

							    if (!($this->precpred($this->ctx, 16))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 16)");
							    }
							    $this->setState(51);
							    $this->match(self::T__2);
							    $this->setState(52);
							    $localContext->right = $this->recursiveExpression(17);
							break;

							case 2:
							    $localContext = new Context\MulDivContext(new Context\ExpressionContext($parentContext, $parentState));
							    $localContext->left = $previousContext;

							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(53);

							    if (!($this->precpred($this->ctx, 14))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 14)");
							    }
							    $this->setState(54);

							    $localContext->op = $this->input->LT(1);
							    $_la = $this->input->LA(1);

							    if (!($_la === self::T__3 || $_la === self::T__4)) {
							    	    $localContext->op = $this->errorHandler->recoverInline($this);
							    } else {
							    	if ($this->input->LA(1) === Token::EOF) {
							    	    $this->matchedEOF = true;
							        }

							    	$this->errorHandler->reportMatch($this);
							    	$this->consume();
							    }
							    $this->setState(55);
							    $localContext->right = $this->recursiveExpression(15);
							break;

							case 3:
							    $localContext = new Context\AddSubContext(new Context\ExpressionContext($parentContext, $parentState));
							    $localContext->left = $previousContext;

							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(56);

							    if (!($this->precpred($this->ctx, 13))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 13)");
							    }
							    $this->setState(57);

							    $localContext->op = $this->input->LT(1);
							    $_la = $this->input->LA(1);

							    if (!($_la === self::T__5 || $_la === self::T__6)) {
							    	    $localContext->op = $this->errorHandler->recoverInline($this);
							    } else {
							    	if ($this->input->LA(1) === Token::EOF) {
							    	    $this->matchedEOF = true;
							        }

							    	$this->errorHandler->reportMatch($this);
							    	$this->consume();
							    }
							    $this->setState(58);
							    $localContext->right = $this->recursiveExpression(14);
							break;

							case 4:
							    $localContext = new Context\PercentageOfPreviousContext(new Context\ExpressionContext($parentContext, $parentState));
							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(59);

							    if (!($this->precpred($this->ctx, 15))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 15)");
							    }
							    $this->setState(60);
							    $this->match(self::T__2);
							break;
						}
					}

					$this->setState(65);
					$this->errorHandler->sync($this);

					$alt = $this->getInterpreter()->adaptivePredict($this->input, 2, $this->ctx);
				}
			} catch (RecognitionException $exception) {
				$localContext->exception = $exception;
				$this->errorHandler->reportError($this, $exception);
				$this->errorHandler->recover($this, $exception);
			} finally {
				$this->unrollRecursionContexts($parentContext);
			}

			return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function booleanOperations(): Context\BooleanOperationsContext
		{
		    $localContext = new Context\BooleanOperationsContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 4, self::RULE_booleanOperations);

		    try {
		        $this->setState(130);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 6, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\LessThanContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(66);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(67);
		        	    $localContext->op = $this->match(self::T__9);
		        	    $this->setState(68);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 2:
		        	    $localContext = new Context\LessThanOrEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(70);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(71);
		        	    $localContext->op = $this->match(self::T__10);
		        	    $this->setState(72);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 3:
		        	    $localContext = new Context\MoreThanContext($localContext);
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(74);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(75);
		        	    $localContext->op = $this->match(self::T__11);
		        	    $this->setState(76);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 4:
		        	    $localContext = new Context\MoreThanOrEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(78);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(79);
		        	    $localContext->op = $this->match(self::T__12);
		        	    $this->setState(80);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 5:
		        	    $localContext = new Context\NotEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 5);
		        	    $this->setState(82);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(83);

		        	    $localContext->op = $this->input->LT(1);
		        	    $_la = $this->input->LA(1);

		        	    if (!($_la === self::T__13 || $_la === self::T__14)) {
		        	    	    $localContext->op = $this->errorHandler->recoverInline($this);
		        	    } else {
		        	    	if ($this->input->LA(1) === Token::EOF) {
		        	    	    $this->matchedEOF = true;
		        	        }

		        	    	$this->errorHandler->reportMatch($this);
		        	    	$this->consume();
		        	    }
		        	    $this->setState(84);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 6:
		        	    $localContext = new Context\IsEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 6);
		        	    $this->setState(86);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(87);
		        	    $localContext->op = $this->match(self::T__15);
		        	    $this->setState(88);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 7:
		        	    $localContext = new Context\NotFunctionContext($localContext);
		        	    $this->enterOuterAlt($localContext, 7);
		        	    $this->setState(90);
		        	    $this->match(self::T__16);
		        	    $this->setState(91);
		        	    $this->match(self::T__0);
		        	    $this->setState(92);
		        	    $this->booleanOperations();
		        	    $this->setState(97);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::T__8) {
		        	    	$this->setState(93);
		        	    	$this->match(self::T__8);
		        	    	$this->setState(94);
		        	    	$this->booleanOperations();
		        	    	$this->setState(99);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(100);
		        	    $this->match(self::T__1);
		        	break;

		        	case 8:
		        	    $localContext = new Context\AndFunctionContext($localContext);
		        	    $this->enterOuterAlt($localContext, 8);
		        	    $this->setState(102);
		        	    $this->match(self::T__17);
		        	    $this->setState(103);
		        	    $this->match(self::T__0);
		        	    $this->setState(104);
		        	    $this->booleanOperations();
		        	    $this->setState(109);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::T__8) {
		        	    	$this->setState(105);
		        	    	$this->match(self::T__8);
		        	    	$this->setState(106);
		        	    	$this->booleanOperations();
		        	    	$this->setState(111);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(112);
		        	    $this->match(self::T__1);
		        	break;

		        	case 9:
		        	    $localContext = new Context\OrFunctionContext($localContext);
		        	    $this->enterOuterAlt($localContext, 9);
		        	    $this->setState(114);
		        	    $this->match(self::T__18);
		        	    $this->setState(115);
		        	    $this->match(self::T__0);
		        	    $this->setState(116);
		        	    $this->booleanOperations();
		        	    $this->setState(121);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::T__8) {
		        	    	$this->setState(117);
		        	    	$this->match(self::T__8);
		        	    	$this->setState(118);
		        	    	$this->booleanOperations();
		        	    	$this->setState(123);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(124);
		        	    $this->match(self::T__1);
		        	break;

		        	case 10:
		        	    $localContext = new Context\BracedBooleanOperationContext($localContext);
		        	    $this->enterOuterAlt($localContext, 10);
		        	    $this->setState(126);
		        	    $this->match(self::T__0);
		        	    $this->setState(127);
		        	    $this->booleanOperations();
		        	    $this->setState(128);
		        	    $this->match(self::T__1);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		public function sempred(?RuleContext $localContext, int $ruleIndex, int $predicateIndex): bool
		{
			switch ($ruleIndex) {
					case 1:
						return $this->sempredExpression($localContext, $predicateIndex);

				default:
					return true;
				}
		}

		private function sempredExpression(?Context\ExpressionContext $localContext, int $predicateIndex): bool
		{
			switch ($predicateIndex) {
			    case 0:
			        return $this->precpred($this->ctx, 16);

			    case 1:
			        return $this->precpred($this->ctx, 14);

			    case 2:
			        return $this->precpred($this->ctx, 13);

			    case 3:
			        return $this->precpred($this->ctx, 15);
			}

			return true;
		}
	}
}

namespace App\FormulaParser\AntlrParser\Context {
	use Antlr\Antlr4\Runtime\ParserRuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
	use Antlr\Antlr4\Runtime\Tree\TerminalNode;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
	use App\FormulaParser\AntlrParser\FormulaParser;
	use App\FormulaParser\AntlrParser\FormulaVisitor;

	class FormulaContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return FormulaParser::RULE_formula;
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function EOF(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::EOF, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitFormula($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class ExpressionContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return FormulaParser::RULE_expression;
	    }

		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class PositiveIntContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function IntegerLiteral(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::IntegerLiteral, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPositiveInt($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PositiveDoubleContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function Double(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::Double, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPositiveDouble($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class MulDivContext extends ExpressionContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitMulDiv($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class AddSubContext extends ExpressionContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitAddSub($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NegativeIntContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function IntegerLiteral(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::IntegerLiteral, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitNegativeInt($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class IFExpressionContext extends ExpressionContext
	{
		/**
		 * @var BooleanOperationsContext|null $condition
		 */
		public $condition;

		/**
		 * @var ExpressionContext|null $then
		 */
		public $then;

		/**
		 * @var ExpressionContext|null $else
		 */
		public $else;

		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function booleanOperations(): ?BooleanOperationsContext
	    {
	    	return $this->getTypedRuleContext(BooleanOperationsContext::class, 0);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitIFExpression($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class DoubleContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function Double(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::Double, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitDouble($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class IntContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function IntegerLiteral(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::IntegerLiteral, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitInt($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PositiveIdContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function Variable(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::Variable, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPositiveId($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PercentageOfPreviousContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPercentageOfPrevious($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NegativeIdContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function Variable(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::Variable, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitNegativeId($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class BracedExpressionContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitBracedExpression($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PositiveExpressionContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPositiveExpression($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class IdContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function Variable(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::Variable, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitId($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PercentageOperationContext extends ExpressionContext
	{
		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPercentageOperation($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NegativeDoubleContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function Double(): ?TerminalNode
	    {
	        return $this->getToken(FormulaParser::Double, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitNegativeDouble($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NegativeExpressionContext extends ExpressionContext
	{
		public function __construct(ExpressionContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitNegativeExpression($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class BooleanOperationsContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return FormulaParser::RULE_booleanOperations;
	    }

		public function copyFrom(ParserRuleContext $context): void
		{
			parent::copyFrom($context);

		}
	}

	class LessThanContext extends BooleanOperationsContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitLessThan($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NotEqualContext extends BooleanOperationsContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitNotEqual($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class LessThanOrEqualContext extends BooleanOperationsContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitLessThanOrEqual($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class MoreThanOrEqualContext extends BooleanOperationsContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitMoreThanOrEqual($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class OrFunctionContext extends BooleanOperationsContext
	{
		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<BooleanOperationsContext>|BooleanOperationsContext|null
	     */
	    public function booleanOperations(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(BooleanOperationsContext::class);
	    	}

	        return $this->getTypedRuleContext(BooleanOperationsContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitOrFunction($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NotFunctionContext extends BooleanOperationsContext
	{
		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<BooleanOperationsContext>|BooleanOperationsContext|null
	     */
	    public function booleanOperations(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(BooleanOperationsContext::class);
	    	}

	        return $this->getTypedRuleContext(BooleanOperationsContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitNotFunction($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class AndFunctionContext extends BooleanOperationsContext
	{
		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<BooleanOperationsContext>|BooleanOperationsContext|null
	     */
	    public function booleanOperations(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(BooleanOperationsContext::class);
	    	}

	        return $this->getTypedRuleContext(BooleanOperationsContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitAndFunction($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class MoreThanContext extends BooleanOperationsContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitMoreThan($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class IsEqualContext extends BooleanOperationsContext
	{
		/**
		 * @var Token|null $op
		 */
		public $op;

		/**
		 * @var ExpressionContext|null $left
		 */
		public $left;

		/**
		 * @var ExpressionContext|null $right
		 */
		public $right;

		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitIsEqual($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class BracedBooleanOperationContext extends BooleanOperationsContext
	{
		public function __construct(BooleanOperationsContext $context)
		{
		    parent::__construct($context);

		    $this->copyFrom($context);
	    }

	    public function booleanOperations(): ?BooleanOperationsContext
	    {
	    	return $this->getTypedRuleContext(BooleanOperationsContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitBracedBooleanOperation($this);
		    }

			return $visitor->visitChildren($this);
		}
	}
}
