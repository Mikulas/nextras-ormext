<?php

namespace Mikulas\Orm\PgExt;

use Nette\Utils\Tokenizer;
use Nette\Utils\TokenizerException;
use Nextras\Dbal\Drivers\IDriver;


/**
 * @see http://www.postgresql.org/docs/9.4/static/arrays.html
 * <code>
 *   { val1 delim val2 delim ... }
 * </code>
 * Each val is either a constant of the array element type, or a subarray.
 *
 * Among the standard data types provided in the PostgreSQL distribution, all use a comma (,).
 *
 * Example:
 * <code>
 *   {{1,2,3},{4,5,6},{7,8,9}}
 * </code>
 */
class PgArray
{

	/** @internal */
	const T_OPEN = 1;
	/** @internal */
	const T_CLOSE = 2;
	/** @internal */
	const T_SEPARATOR = 3;
	/** @internal */
	const T_VALUE = 4;
	/** @internal */
	const T_QUOTED_VALUE = 5;


	/**
	 * @param array    $input
	 * @param callable $transform        (mixed $partial)
	 * @param bool     $castEmptyToNull
	 * @return NULL|string
	 */
	public static function serialize(array $input = NULL, callable $transform , $castEmptyToNull = FALSE)
	{
		if ($input === NULL || ($castEmptyToNull && !$input)) {
			return NULL;
		}

		$values = [];
		foreach ($input as $partial) {
			if (is_array($partial)) {
				$values[] = self::serialize($partial, $transform, $castEmptyToNull);
			} else {
				$values[] = $transform($partial);
			}
		}
		return '{' . implode(',', $values) . '}';
	}


	/**
	 * @param string   $input
	 * @param callable $transform (mixed $partial) called for each item
	 * @return array|NULL
	 * @throws PgArrayException
	 */
	public static function parse($input, callable $transform)
	{
		if ($input === NULL) {
			return NULL;
		}

		$tokenizer = new Tokenizer([
			self::T_OPEN => '\s*\{',
			self::T_CLOSE => '\}\s*',
			self::T_SEPARATOR => ',',
			self::T_QUOTED_VALUE => '\s*"(?:""|[^"])*"\s*',
			self::T_VALUE => '[^,{}"\\\\]+',
		]);

		try {
			$tokens = $tokenizer->tokenize($input);

		} catch (TokenizerException $e) {
			throw PgArrayException::tokenizerFailure($e);
		}

		list($value, $offset, $type) = $tokens[0];
		if ($type !== self::T_OPEN) {
			throw PgArrayException::openFailed();
		}

		list($values, $position) = self::innerParse($tokens, $transform, 1);

		if ($position !== count($tokens)) {
			throw PgArrayException::mismatchedBrackets();
		}

		return $values;
	}

	/**
	 * @param array    $tokens
	 * @param callable $transform (mixed $partial) called for each item
	 * @param int      $startPos  1..count($tokens)
	 * @return array   [array|NULL $value, int $newPosition]
	 * @throws PgArrayException
	 */
	protected static function innerParse($tokens, callable $transform, $startPos)
	{
		$values = [];
		$previousType = NULL;
		for ($position = $startPos; $position < count($tokens); ++$position) {
			list($value, $offset, $type) = $tokens[$position];
			if ($type === self::T_OPEN) {
				list($values[], $position) = self::innerParse($tokens, $transform, $position + 1);

			} elseif ($type === self::T_CLOSE) {
				if ($previousType === self::T_SEPARATOR) {
					$values[] = $transform(NULL);
				}
				return [$values, $position + 1];

			} elseif ($type === self::T_SEPARATOR) {
				if ($previousType === self::T_SEPARATOR) {
					$values[] = $transform(NULL);
				}

			} elseif ($type === self::T_VALUE) {
				$value = $value ? trim($value) : $value;
				$values[] = $transform($value);

			} elseif ($type === self::T_QUOTED_VALUE) {
				$value = str_replace('""', '"', substr(trim($value), 1, -1));
				$values[] = $transform($value);

			} else {
				throw PgArrayException::malformedInput();
			}

			$previousType = $type;
		}
		return [$values, $position];
	}

}
