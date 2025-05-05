<?php

namespace App\FormulaParser\AntlrParser {
	use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
	use Antlr\Antlr4\Runtime\Atn\LexerATNSimulator;
	use Antlr\Antlr4\Runtime\Lexer;
	use Antlr\Antlr4\Runtime\CharStream;
	use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
	use Antlr\Antlr4\Runtime\RuleContext;
	use Antlr\Antlr4\Runtime\Atn\ATN;
	use Antlr\Antlr4\Runtime\Dfa\DFA;
	use Antlr\Antlr4\Runtime\Vocabulary;
	use Antlr\Antlr4\Runtime\RuntimeMetaData;
	use Antlr\Antlr4\Runtime\VocabularyImpl;

	final class FormulaLexer extends Lexer
	{
		public const T__0 = 1, T__1 = 2, T__2 = 3, T__3 = 4, T__4 = 5, T__5 = 6,
               T__6 = 7, T__7 = 8, T__8 = 9, T__9 = 10, T__10 = 11, T__11 = 12,
               T__12 = 13, T__13 = 14, T__14 = 15, T__15 = 16, T__16 = 17,
               T__17 = 18, T__18 = 19, Variable = 20, IntegerLiteral = 21,
               Double = 22, WS = 23;

		/**
		 * @var array<string>
		 */
		public const CHANNEL_NAMES = [
			'DEFAULT_TOKEN_CHANNEL', 'HIDDEN'
		];

		/**
		 * @var array<string>
		 */
		public const MODE_NAMES = [
			'DEFAULT_MODE'
		];

		/**
		 * @var array<string>
		 */
		public const RULE_NAMES = [
			'T__0', 'T__1', 'T__2', 'T__3', 'T__4', 'T__5', 'T__6', 'T__7', 'T__8',
			'T__9', 'T__10', 'T__11', 'T__12', 'T__13', 'T__14', 'T__15', 'T__16',
			'T__17', 'T__18', 'Variable', 'IntegerLiteral', 'Double', 'WS'
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
			[4, 0, 23, 122, 6, -1, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 2, 3, 7, 3,
		    2, 4, 7, 4, 2, 5, 7, 5, 2, 6, 7, 6, 2, 7, 7, 7, 2, 8, 7, 8, 2, 9,
		    7, 9, 2, 10, 7, 10, 2, 11, 7, 11, 2, 12, 7, 12, 2, 13, 7, 13, 2, 14,
		    7, 14, 2, 15, 7, 15, 2, 16, 7, 16, 2, 17, 7, 17, 2, 18, 7, 18, 2,
		    19, 7, 19, 2, 20, 7, 20, 2, 21, 7, 21, 2, 22, 7, 22, 1, 0, 1, 0, 1,
		    1, 1, 1, 1, 2, 1, 2, 1, 3, 1, 3, 1, 4, 1, 4, 1, 5, 1, 5, 1, 6, 1,
		    6, 1, 7, 1, 7, 1, 7, 1, 8, 1, 8, 1, 9, 1, 9, 1, 10, 1, 10, 1, 10,
		    1, 11, 1, 11, 1, 12, 1, 12, 1, 12, 1, 13, 1, 13, 1, 13, 1, 14, 1,
		    14, 1, 14, 1, 15, 1, 15, 1, 16, 1, 16, 1, 16, 1, 16, 1, 17, 1, 17,
		    1, 17, 1, 17, 1, 18, 1, 18, 1, 18, 1, 19, 1, 19, 5, 19, 98, 8, 19,
		    10, 19, 12, 19, 101, 9, 19, 1, 20, 4, 20, 104, 8, 20, 11, 20, 12,
		    20, 105, 1, 21, 1, 21, 1, 21, 1, 21, 1, 21, 1, 21, 3, 21, 114, 8,
		    21, 1, 22, 4, 22, 117, 8, 22, 11, 22, 12, 22, 118, 1, 22, 1, 22, 0,
		    0, 23, 1, 1, 3, 2, 5, 3, 7, 4, 9, 5, 11, 6, 13, 7, 15, 8, 17, 9, 19,
		    10, 21, 11, 23, 12, 25, 13, 27, 14, 29, 15, 31, 16, 33, 17, 35, 18,
		    37, 19, 39, 20, 41, 21, 43, 22, 45, 23, 1, 0, 4, 3, 0, 65, 90, 95,
		    95, 97, 122, 4, 0, 48, 57, 65, 90, 95, 95, 97, 122, 1, 0, 48, 57,
		    3, 0, 9, 10, 13, 13, 32, 32, 125, 0, 1, 1, 0, 0, 0, 0, 3, 1, 0, 0,
		    0, 0, 5, 1, 0, 0, 0, 0, 7, 1, 0, 0, 0, 0, 9, 1, 0, 0, 0, 0, 11, 1,
		    0, 0, 0, 0, 13, 1, 0, 0, 0, 0, 15, 1, 0, 0, 0, 0, 17, 1, 0, 0, 0,
		    0, 19, 1, 0, 0, 0, 0, 21, 1, 0, 0, 0, 0, 23, 1, 0, 0, 0, 0, 25, 1,
		    0, 0, 0, 0, 27, 1, 0, 0, 0, 0, 29, 1, 0, 0, 0, 0, 31, 1, 0, 0, 0,
		    0, 33, 1, 0, 0, 0, 0, 35, 1, 0, 0, 0, 0, 37, 1, 0, 0, 0, 0, 39, 1,
		    0, 0, 0, 0, 41, 1, 0, 0, 0, 0, 43, 1, 0, 0, 0, 0, 45, 1, 0, 0, 0,
		    1, 47, 1, 0, 0, 0, 3, 49, 1, 0, 0, 0, 5, 51, 1, 0, 0, 0, 7, 53, 1,
		    0, 0, 0, 9, 55, 1, 0, 0, 0, 11, 57, 1, 0, 0, 0, 13, 59, 1, 0, 0, 0,
		    15, 61, 1, 0, 0, 0, 17, 64, 1, 0, 0, 0, 19, 66, 1, 0, 0, 0, 21, 68,
		    1, 0, 0, 0, 23, 71, 1, 0, 0, 0, 25, 73, 1, 0, 0, 0, 27, 76, 1, 0,
		    0, 0, 29, 79, 1, 0, 0, 0, 31, 82, 1, 0, 0, 0, 33, 84, 1, 0, 0, 0,
		    35, 88, 1, 0, 0, 0, 37, 92, 1, 0, 0, 0, 39, 95, 1, 0, 0, 0, 41, 103,
		    1, 0, 0, 0, 43, 113, 1, 0, 0, 0, 45, 116, 1, 0, 0, 0, 47, 48, 5, 40,
		    0, 0, 48, 2, 1, 0, 0, 0, 49, 50, 5, 41, 0, 0, 50, 4, 1, 0, 0, 0, 51,
		    52, 5, 37, 0, 0, 52, 6, 1, 0, 0, 0, 53, 54, 5, 42, 0, 0, 54, 8, 1,
		    0, 0, 0, 55, 56, 5, 47, 0, 0, 56, 10, 1, 0, 0, 0, 57, 58, 5, 43, 0,
		    0, 58, 12, 1, 0, 0, 0, 59, 60, 5, 45, 0, 0, 60, 14, 1, 0, 0, 0, 61,
		    62, 5, 73, 0, 0, 62, 63, 5, 70, 0, 0, 63, 16, 1, 0, 0, 0, 64, 65,
		    5, 44, 0, 0, 65, 18, 1, 0, 0, 0, 66, 67, 5, 60, 0, 0, 67, 20, 1, 0,
		    0, 0, 68, 69, 5, 60, 0, 0, 69, 70, 5, 61, 0, 0, 70, 22, 1, 0, 0, 0,
		    71, 72, 5, 62, 0, 0, 72, 24, 1, 0, 0, 0, 73, 74, 5, 62, 0, 0, 74,
		    75, 5, 61, 0, 0, 75, 26, 1, 0, 0, 0, 76, 77, 5, 33, 0, 0, 77, 78,
		    5, 61, 0, 0, 78, 28, 1, 0, 0, 0, 79, 80, 5, 60, 0, 0, 80, 81, 5, 62,
		    0, 0, 81, 30, 1, 0, 0, 0, 82, 83, 5, 61, 0, 0, 83, 32, 1, 0, 0, 0,
		    84, 85, 5, 78, 0, 0, 85, 86, 5, 79, 0, 0, 86, 87, 5, 84, 0, 0, 87,
		    34, 1, 0, 0, 0, 88, 89, 5, 65, 0, 0, 89, 90, 5, 78, 0, 0, 90, 91,
		    5, 68, 0, 0, 91, 36, 1, 0, 0, 0, 92, 93, 5, 79, 0, 0, 93, 94, 5, 82,
		    0, 0, 94, 38, 1, 0, 0, 0, 95, 99, 7, 0, 0, 0, 96, 98, 7, 1, 0, 0,
		    97, 96, 1, 0, 0, 0, 98, 101, 1, 0, 0, 0, 99, 97, 1, 0, 0, 0, 99, 100,
		    1, 0, 0, 0, 100, 40, 1, 0, 0, 0, 101, 99, 1, 0, 0, 0, 102, 104, 7,
		    2, 0, 0, 103, 102, 1, 0, 0, 0, 104, 105, 1, 0, 0, 0, 105, 103, 1,
		    0, 0, 0, 105, 106, 1, 0, 0, 0, 106, 42, 1, 0, 0, 0, 107, 108, 3, 41,
		    20, 0, 108, 109, 5, 46, 0, 0, 109, 110, 3, 41, 20, 0, 110, 114, 1,
		    0, 0, 0, 111, 112, 5, 46, 0, 0, 112, 114, 3, 41, 20, 0, 113, 107,
		    1, 0, 0, 0, 113, 111, 1, 0, 0, 0, 114, 44, 1, 0, 0, 0, 115, 117, 7,
		    3, 0, 0, 116, 115, 1, 0, 0, 0, 117, 118, 1, 0, 0, 0, 118, 116, 1,
		    0, 0, 0, 118, 119, 1, 0, 0, 0, 119, 120, 1, 0, 0, 0, 120, 121, 6,
		    22, 0, 0, 121, 46, 1, 0, 0, 0, 5, 0, 99, 105, 113, 118, 1, 6, 0, 0];
		protected static $atn;
		protected static $decisionToDFA;
		protected static $sharedContextCache;
		public function __construct(CharStream $input)
		{
			parent::__construct($input);

			self::initialize();

			$this->interp = new LexerATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
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

		public static function vocabulary(): Vocabulary
		{
			static $vocabulary;

			return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
		}

		public function getGrammarFileName(): string
		{
			return 'Formula.g4';
		}

		public function getRuleNames(): array
		{
			return self::RULE_NAMES;
		}

		public function getSerializedATN(): array
		{
			return self::SERIALIZED_ATN;
		}

		/**
		 * @return array<string>
		 */
		public function getChannelNames(): array
		{
			return self::CHANNEL_NAMES;
		}

		/**
		 * @return array<string>
		 */
		public function getModeNames(): array
		{
			return self::MODE_NAMES;
		}

		public function getATN(): ATN
		{
			return self::$atn;
		}

		public function getVocabulary(): Vocabulary
		{
			return self::vocabulary();
		}
	}
}
