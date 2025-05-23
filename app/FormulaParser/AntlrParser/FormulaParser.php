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
			[4, 1, 23, 153, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 1, 0, 1, 0, 1, 0,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 3, 1, 69, 8, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
		    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 5, 1, 82, 8, 1, 10, 1, 12,
		    1, 85, 9, 1, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2,
		    1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2,
		    1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 5, 2, 116, 8,
		    2, 10, 2, 12, 2, 119, 9, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1,
		    2, 5, 2, 128, 8, 2, 10, 2, 12, 2, 131, 9, 2, 1, 2, 1, 2, 1, 2, 1,
		    2, 1, 2, 1, 2, 1, 2, 5, 2, 140, 8, 2, 10, 2, 12, 2, 143, 9, 2, 1,
		    2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 3, 2, 151, 8, 2, 1, 2, 0, 1, 2, 3,
		    0, 2, 4, 0, 3, 1, 0, 4, 5, 1, 0, 6, 7, 1, 0, 14, 15, 179, 0, 6, 1,
		    0, 0, 0, 2, 68, 1, 0, 0, 0, 4, 150, 1, 0, 0, 0, 6, 7, 3, 2, 1, 0,
		    7, 8, 5, 0, 0, 1, 8, 1, 1, 0, 0, 0, 9, 10, 6, 1, -1, 0, 10, 11, 5,
		    1, 0, 0, 11, 12, 3, 2, 1, 0, 12, 13, 5, 2, 0, 0, 13, 69, 1, 0, 0,
		    0, 14, 15, 5, 7, 0, 0, 15, 16, 5, 1, 0, 0, 16, 17, 3, 2, 1, 0, 17,
		    18, 5, 2, 0, 0, 18, 69, 1, 0, 0, 0, 19, 20, 5, 6, 0, 0, 20, 21, 5,
		    1, 0, 0, 21, 22, 3, 2, 1, 0, 22, 23, 5, 2, 0, 0, 23, 69, 1, 0, 0,
		    0, 24, 25, 5, 8, 0, 0, 25, 26, 5, 1, 0, 0, 26, 27, 3, 4, 2, 0, 27,
		    28, 5, 9, 0, 0, 28, 29, 3, 2, 1, 0, 29, 30, 5, 9, 0, 0, 30, 31, 3,
		    2, 1, 0, 31, 32, 5, 2, 0, 0, 32, 69, 1, 0, 0, 0, 33, 34, 5, 7, 0,
		    0, 34, 35, 5, 8, 0, 0, 35, 36, 5, 1, 0, 0, 36, 37, 3, 4, 2, 0, 37,
		    38, 5, 9, 0, 0, 38, 39, 3, 2, 1, 0, 39, 40, 5, 9, 0, 0, 40, 41, 3,
		    2, 1, 0, 41, 42, 5, 2, 0, 0, 42, 69, 1, 0, 0, 0, 43, 44, 5, 6, 0,
		    0, 44, 45, 5, 8, 0, 0, 45, 46, 5, 1, 0, 0, 46, 47, 3, 4, 2, 0, 47,
		    48, 5, 9, 0, 0, 48, 49, 3, 2, 1, 0, 49, 50, 5, 9, 0, 0, 50, 51, 3,
		    2, 1, 0, 51, 52, 5, 2, 0, 0, 52, 69, 1, 0, 0, 0, 53, 69, 5, 20, 0,
		    0, 54, 69, 5, 21, 0, 0, 55, 69, 5, 22, 0, 0, 56, 57, 5, 7, 0, 0, 57,
		    69, 5, 20, 0, 0, 58, 59, 5, 7, 0, 0, 59, 69, 5, 21, 0, 0, 60, 61,
		    5, 7, 0, 0, 61, 69, 5, 22, 0, 0, 62, 63, 5, 6, 0, 0, 63, 69, 5, 20,
		    0, 0, 64, 65, 5, 6, 0, 0, 65, 69, 5, 21, 0, 0, 66, 67, 5, 6, 0, 0,
		    67, 69, 5, 22, 0, 0, 68, 9, 1, 0, 0, 0, 68, 14, 1, 0, 0, 0, 68, 19,
		    1, 0, 0, 0, 68, 24, 1, 0, 0, 0, 68, 33, 1, 0, 0, 0, 68, 43, 1, 0,
		    0, 0, 68, 53, 1, 0, 0, 0, 68, 54, 1, 0, 0, 0, 68, 55, 1, 0, 0, 0,
		    68, 56, 1, 0, 0, 0, 68, 58, 1, 0, 0, 0, 68, 60, 1, 0, 0, 0, 68, 62,
		    1, 0, 0, 0, 68, 64, 1, 0, 0, 0, 68, 66, 1, 0, 0, 0, 69, 83, 1, 0,
		    0, 0, 70, 71, 10, 18, 0, 0, 71, 72, 5, 3, 0, 0, 72, 82, 3, 2, 1, 19,
		    73, 74, 10, 16, 0, 0, 74, 75, 7, 0, 0, 0, 75, 82, 3, 2, 1, 17, 76,
		    77, 10, 15, 0, 0, 77, 78, 7, 1, 0, 0, 78, 82, 3, 2, 1, 16, 79, 80,
		    10, 17, 0, 0, 80, 82, 5, 3, 0, 0, 81, 70, 1, 0, 0, 0, 81, 73, 1, 0,
		    0, 0, 81, 76, 1, 0, 0, 0, 81, 79, 1, 0, 0, 0, 82, 85, 1, 0, 0, 0,
		    83, 81, 1, 0, 0, 0, 83, 84, 1, 0, 0, 0, 84, 3, 1, 0, 0, 0, 85, 83,
		    1, 0, 0, 0, 86, 87, 3, 2, 1, 0, 87, 88, 5, 10, 0, 0, 88, 89, 3, 2,
		    1, 0, 89, 151, 1, 0, 0, 0, 90, 91, 3, 2, 1, 0, 91, 92, 5, 11, 0, 0,
		    92, 93, 3, 2, 1, 0, 93, 151, 1, 0, 0, 0, 94, 95, 3, 2, 1, 0, 95, 96,
		    5, 12, 0, 0, 96, 97, 3, 2, 1, 0, 97, 151, 1, 0, 0, 0, 98, 99, 3, 2,
		    1, 0, 99, 100, 5, 13, 0, 0, 100, 101, 3, 2, 1, 0, 101, 151, 1, 0,
		    0, 0, 102, 103, 3, 2, 1, 0, 103, 104, 7, 2, 0, 0, 104, 105, 3, 2,
		    1, 0, 105, 151, 1, 0, 0, 0, 106, 107, 3, 2, 1, 0, 107, 108, 5, 16,
		    0, 0, 108, 109, 3, 2, 1, 0, 109, 151, 1, 0, 0, 0, 110, 111, 5, 17,
		    0, 0, 111, 112, 5, 1, 0, 0, 112, 117, 3, 4, 2, 0, 113, 114, 5, 9,
		    0, 0, 114, 116, 3, 4, 2, 0, 115, 113, 1, 0, 0, 0, 116, 119, 1, 0,
		    0, 0, 117, 115, 1, 0, 0, 0, 117, 118, 1, 0, 0, 0, 118, 120, 1, 0,
		    0, 0, 119, 117, 1, 0, 0, 0, 120, 121, 5, 2, 0, 0, 121, 151, 1, 0,
		    0, 0, 122, 123, 5, 18, 0, 0, 123, 124, 5, 1, 0, 0, 124, 129, 3, 4,
		    2, 0, 125, 126, 5, 9, 0, 0, 126, 128, 3, 4, 2, 0, 127, 125, 1, 0,
		    0, 0, 128, 131, 1, 0, 0, 0, 129, 127, 1, 0, 0, 0, 129, 130, 1, 0,
		    0, 0, 130, 132, 1, 0, 0, 0, 131, 129, 1, 0, 0, 0, 132, 133, 5, 2,
		    0, 0, 133, 151, 1, 0, 0, 0, 134, 135, 5, 19, 0, 0, 135, 136, 5, 1,
		    0, 0, 136, 141, 3, 4, 2, 0, 137, 138, 5, 9, 0, 0, 138, 140, 3, 4,
		    2, 0, 139, 137, 1, 0, 0, 0, 140, 143, 1, 0, 0, 0, 141, 139, 1, 0,
		    0, 0, 141, 142, 1, 0, 0, 0, 142, 144, 1, 0, 0, 0, 143, 141, 1, 0,
		    0, 0, 144, 145, 5, 2, 0, 0, 145, 151, 1, 0, 0, 0, 146, 147, 5, 1,
		    0, 0, 147, 148, 3, 4, 2, 0, 148, 149, 5, 2, 0, 0, 149, 151, 1, 0,
		    0, 0, 150, 86, 1, 0, 0, 0, 150, 90, 1, 0, 0, 0, 150, 94, 1, 0, 0,
		    0, 150, 98, 1, 0, 0, 0, 150, 102, 1, 0, 0, 0, 150, 106, 1, 0, 0, 0,
		    150, 110, 1, 0, 0, 0, 150, 122, 1, 0, 0, 0, 150, 134, 1, 0, 0, 0,
		    150, 146, 1, 0, 0, 0, 151, 5, 1, 0, 0, 0, 7, 68, 81, 83, 117, 129,
		    141, 150];
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
				$this->setState(68);
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
					    $localContext = new Context\NegativeIFExpressionContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(33);
					    $this->match(self::T__6);
					    $this->setState(34);
					    $this->match(self::T__7);
					    $this->setState(35);
					    $this->match(self::T__0);
					    $this->setState(36);
					    $localContext->condition = $this->booleanOperations();
					    $this->setState(37);
					    $this->match(self::T__8);
					    $this->setState(38);
					    $localContext->then = $this->recursiveExpression(0);
					    $this->setState(39);
					    $this->match(self::T__8);
					    $this->setState(40);
					    $localContext->else = $this->recursiveExpression(0);
					    $this->setState(41);
					    $this->match(self::T__1);
					break;

					case 6:
					    $localContext = new Context\PositiveIFExpressionContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(43);
					    $this->match(self::T__5);
					    $this->setState(44);
					    $this->match(self::T__7);
					    $this->setState(45);
					    $this->match(self::T__0);
					    $this->setState(46);
					    $localContext->condition = $this->booleanOperations();
					    $this->setState(47);
					    $this->match(self::T__8);
					    $this->setState(48);
					    $localContext->then = $this->recursiveExpression(0);
					    $this->setState(49);
					    $this->match(self::T__8);
					    $this->setState(50);
					    $localContext->else = $this->recursiveExpression(0);
					    $this->setState(51);
					    $this->match(self::T__1);
					break;

					case 7:
					    $localContext = new Context\IdContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(53);
					    $this->match(self::Variable);
					break;

					case 8:
					    $localContext = new Context\IntContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(54);
					    $this->match(self::IntegerLiteral);
					break;

					case 9:
					    $localContext = new Context\DoubleContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(55);
					    $this->match(self::Double);
					break;

					case 10:
					    $localContext = new Context\NegativeIdContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(56);
					    $this->match(self::T__6);
					    $this->setState(57);
					    $this->match(self::Variable);
					break;

					case 11:
					    $localContext = new Context\NegativeIntContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(58);
					    $this->match(self::T__6);
					    $this->setState(59);
					    $this->match(self::IntegerLiteral);
					break;

					case 12:
					    $localContext = new Context\NegativeDoubleContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(60);
					    $this->match(self::T__6);
					    $this->setState(61);
					    $this->match(self::Double);
					break;

					case 13:
					    $localContext = new Context\PositiveIdContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(62);
					    $this->match(self::T__5);
					    $this->setState(63);
					    $this->match(self::Variable);
					break;

					case 14:
					    $localContext = new Context\PositiveIntContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(64);
					    $this->match(self::T__5);
					    $this->setState(65);
					    $this->match(self::IntegerLiteral);
					break;

					case 15:
					    $localContext = new Context\PositiveDoubleContext($localContext);
					    $this->ctx = $localContext;
					    $previousContext = $localContext;
					    $this->setState(66);
					    $this->match(self::T__5);
					    $this->setState(67);
					    $this->match(self::Double);
					break;
				}
				$this->ctx->stop = $this->input->LT(-1);
				$this->setState(83);
				$this->errorHandler->sync($this);

				$alt = $this->getInterpreter()->adaptivePredict($this->input, 2, $this->ctx);

				while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
					if ($alt === 1) {
						if ($this->getParseListeners() !== null) {
						    $this->triggerExitRuleEvent();
						}

						$previousContext = $localContext;
						$this->setState(81);
						$this->errorHandler->sync($this);

						switch ($this->getInterpreter()->adaptivePredict($this->input, 1, $this->ctx)) {
							case 1:
							    $localContext = new Context\PercentageOperationContext(new Context\ExpressionContext($parentContext, $parentState));
							    $localContext->left = $previousContext;

							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(70);

							    if (!($this->precpred($this->ctx, 18))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 18)");
							    }
							    $this->setState(71);
							    $this->match(self::T__2);
							    $this->setState(72);
							    $localContext->right = $this->recursiveExpression(19);
							break;

							case 2:
							    $localContext = new Context\MulDivContext(new Context\ExpressionContext($parentContext, $parentState));
							    $localContext->left = $previousContext;

							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(73);

							    if (!($this->precpred($this->ctx, 16))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 16)");
							    }
							    $this->setState(74);

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
							    $this->setState(75);
							    $localContext->right = $this->recursiveExpression(17);
							break;

							case 3:
							    $localContext = new Context\AddSubContext(new Context\ExpressionContext($parentContext, $parentState));
							    $localContext->left = $previousContext;

							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(76);

							    if (!($this->precpred($this->ctx, 15))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 15)");
							    }
							    $this->setState(77);

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
							    $this->setState(78);
							    $localContext->right = $this->recursiveExpression(16);
							break;

							case 4:
							    $localContext = new Context\PercentageOfPreviousContext(new Context\ExpressionContext($parentContext, $parentState));
							    $this->pushNewRecursionContext($localContext, $startState, self::RULE_expression);
							    $this->setState(79);

							    if (!($this->precpred($this->ctx, 17))) {
							        throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 17)");
							    }
							    $this->setState(80);
							    $this->match(self::T__2);
							break;
						}
					}

					$this->setState(85);
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
		        $this->setState(150);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 6, $this->ctx)) {
		        	case 1:
		        	    $localContext = new Context\LessThanContext($localContext);
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(86);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(87);
		        	    $localContext->op = $this->match(self::T__9);
		        	    $this->setState(88);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 2:
		        	    $localContext = new Context\LessThanOrEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(90);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(91);
		        	    $localContext->op = $this->match(self::T__10);
		        	    $this->setState(92);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 3:
		        	    $localContext = new Context\MoreThanContext($localContext);
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(94);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(95);
		        	    $localContext->op = $this->match(self::T__11);
		        	    $this->setState(96);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 4:
		        	    $localContext = new Context\MoreThanOrEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(98);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(99);
		        	    $localContext->op = $this->match(self::T__12);
		        	    $this->setState(100);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 5:
		        	    $localContext = new Context\NotEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 5);
		        	    $this->setState(102);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(103);

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
		        	    $this->setState(104);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 6:
		        	    $localContext = new Context\IsEqualContext($localContext);
		        	    $this->enterOuterAlt($localContext, 6);
		        	    $this->setState(106);
		        	    $localContext->left = $this->recursiveExpression(0);
		        	    $this->setState(107);
		        	    $localContext->op = $this->match(self::T__15);
		        	    $this->setState(108);
		        	    $localContext->right = $this->recursiveExpression(0);
		        	break;

		        	case 7:
		        	    $localContext = new Context\NotFunctionContext($localContext);
		        	    $this->enterOuterAlt($localContext, 7);
		        	    $this->setState(110);
		        	    $this->match(self::T__16);
		        	    $this->setState(111);
		        	    $this->match(self::T__0);
		        	    $this->setState(112);
		        	    $this->booleanOperations();
		        	    $this->setState(117);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::T__8) {
		        	    	$this->setState(113);
		        	    	$this->match(self::T__8);
		        	    	$this->setState(114);
		        	    	$this->booleanOperations();
		        	    	$this->setState(119);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(120);
		        	    $this->match(self::T__1);
		        	break;

		        	case 8:
		        	    $localContext = new Context\AndFunctionContext($localContext);
		        	    $this->enterOuterAlt($localContext, 8);
		        	    $this->setState(122);
		        	    $this->match(self::T__17);
		        	    $this->setState(123);
		        	    $this->match(self::T__0);
		        	    $this->setState(124);
		        	    $this->booleanOperations();
		        	    $this->setState(129);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::T__8) {
		        	    	$this->setState(125);
		        	    	$this->match(self::T__8);
		        	    	$this->setState(126);
		        	    	$this->booleanOperations();
		        	    	$this->setState(131);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(132);
		        	    $this->match(self::T__1);
		        	break;

		        	case 9:
		        	    $localContext = new Context\OrFunctionContext($localContext);
		        	    $this->enterOuterAlt($localContext, 9);
		        	    $this->setState(134);
		        	    $this->match(self::T__18);
		        	    $this->setState(135);
		        	    $this->match(self::T__0);
		        	    $this->setState(136);
		        	    $this->booleanOperations();
		        	    $this->setState(141);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::T__8) {
		        	    	$this->setState(137);
		        	    	$this->match(self::T__8);
		        	    	$this->setState(138);
		        	    	$this->booleanOperations();
		        	    	$this->setState(143);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(144);
		        	    $this->match(self::T__1);
		        	break;

		        	case 10:
		        	    $localContext = new Context\BracedBooleanOperationContext($localContext);
		        	    $this->enterOuterAlt($localContext, 10);
		        	    $this->setState(146);
		        	    $this->match(self::T__0);
		        	    $this->setState(147);
		        	    $this->booleanOperations();
		        	    $this->setState(148);
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
			        return $this->precpred($this->ctx, 18);

			    case 1:
			        return $this->precpred($this->ctx, 16);

			    case 2:
			        return $this->precpred($this->ctx, 15);

			    case 3:
			        return $this->precpred($this->ctx, 17);
			}

			return true;
		}
	}
}

namespace app\FormulaParser\AntlrParser\Context {
	use Antlr\Antlr4\Runtime\ParserRuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
	use Antlr\Antlr4\Runtime\Tree\TerminalNode;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
	use app\FormulaParser\AntlrParser\FormulaParser;
	use app\FormulaParser\AntlrParser\FormulaVisitor;
	use app\FormulaParser\AntlrParser\FormulaListener;

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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterFormula($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitFormula($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterPositiveInt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitPositiveInt($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPositiveInt($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class PositiveIFExpressionContext extends ExpressionContext
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterPositiveIFExpression($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitPositiveIFExpression($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPositiveIFExpression($this);
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterPositiveDouble($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitPositiveDouble($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterMulDiv($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitMulDiv($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterAddSub($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitAddSub($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterNegativeInt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitNegativeInt($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterIFExpression($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitIFExpression($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterDouble($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitDouble($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterInt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitInt($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterPositiveId($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitPositiveId($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitPositiveId($this);
		    }

			return $visitor->visitChildren($this);
		}
	}

	class NegativeIFExpressionContext extends ExpressionContext
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterNegativeIFExpression($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitNegativeIFExpression($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof FormulaVisitor) {
			    return $visitor->visitNegativeIFExpression($this);
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterPercentageOfPrevious($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitPercentageOfPrevious($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterNegativeId($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitNegativeId($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterBracedExpression($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitBracedExpression($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterPositiveExpression($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitPositiveExpression($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterId($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitId($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterPercentageOperation($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitPercentageOperation($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterNegativeDouble($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitNegativeDouble($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterNegativeExpression($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitNegativeExpression($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterLessThan($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitLessThan($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterNotEqual($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitNotEqual($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterLessThanOrEqual($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitLessThanOrEqual($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterMoreThanOrEqual($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitMoreThanOrEqual($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterOrFunction($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitOrFunction($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterNotFunction($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitNotFunction($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterAndFunction($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitAndFunction($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterMoreThan($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitMoreThan($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterIsEqual($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitIsEqual($this);
		    }
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

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->enterBracedBooleanOperation($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof FormulaListener) {
			    $listener->exitBracedBooleanOperation($this);
		    }
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
