<?php

namespace Mikulas\OrmExt\Pg;

use Nette\Utils\Tokenizer;
use Nette\Utils\TokenizerException;


/**
 * @see http://www.postgresql.org/docs/9.4/static/rowtypes.html
 */
class CompositeType
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
	 * @param callable $transform (mixed $partial)
	 * @return string
	 */
	public static function serialize(array $input, callable $transform = NULL)
	{
		if ($transform === NULL) {
			$transform = function($partial) {
				if (preg_match('~^[\w ]*$~', $partial)) {
					return $partial;
				}
				return self::escapeString($partial);
			};
		}

		$values = [];
		foreach ($input as $value) {
			if (is_array($value)) {
				$values[] = self::serialize($value, $transform);

			} elseif ($value === NULL) {
				$values[] = '';

			} else {
				$values[] = $transform($value);
			}
		}

		return '(' . implode(',', $values) . ')';
	}


	/**
	 * @param string $input
	 * @return array|NULL
	 * @throws CompositeTypeException
	 */
	public static function parse($input)
	{
		if ($input === NULL) {
			return NULL;
		}

		$tokenizer = new Tokenizer([
			self::T_OPEN => '\s*\(',
			self::T_CLOSE => '\)\s*',
			self::T_SEPARATOR => ',',
			self::T_QUOTED_VALUE => '\s*"(?:""|\\\\"|[^"])*"\s*',
			self::T_VALUE => '[^,()"\\\\]+',
		]);

		try {
			$tokens = $tokenizer->tokenize($input);

		} catch (TokenizerException $e) {
			throw CompositeTypeException::tokenizerFailure($input, $e);
		}

		list($value, $offset, $type) = $tokens[0];
		if ($type !== self::T_OPEN) {
			throw CompositeTypeException::openFailed($input);
		}

		list($values, $position) = self::innerParse($input, $tokens, 1);

		if ($position !== count($tokens)) {
			throw CompositeTypeException::mismatchedParens($input);
		}

		// A completely empty field value (no characters at all between the commas or parentheses) represents a NULL.
		if ([] === array_filter($values, function($value) {
			return $value !== NULL;
		})) {
			return NULL;
		}

		return $values;
	}


	/**
	 * @param string   $input
	 * @param array    $tokens
	 * @param int      $startPos
	 * @return array   [array|NULL $value, int $newPosition]
	 */
	protected static function innerParse($input, $tokens, $startPos)
	{
		$values = [];
		$max = count($tokens);
		for ($position = $startPos; $position < $max; ++$position) {
			list($value, $offset, $type) = $tokens[$position];
			$previousType = $position === 0 ? NULL : $tokens[$position - 1][2];

			if ($type === self::T_OPEN) {
				list($values[], $position) = self::innerParse($input, $tokens, $position + 1);

			} elseif ($type === self::T_CLOSE) {
				if ($previousType === self::T_OPEN || $previousType === self::T_SEPARATOR) {
					$values[] = NULL;
				}
				return [$values, $position + 1];

			} elseif ($type === self::T_SEPARATOR) {
				if ($previousType === self::T_OPEN || $previousType === self::T_SEPARATOR) {
					$values[] = NULL;
				}

			} elseif ($type === self::T_VALUE) {
				if (ctype_digit(trim($value))) {
					$values[] = (int) $value;
				} else {
					$values[] = $value;
				}

			} elseif ($type === self::T_QUOTED_VALUE) {
				$values[] = strtr(substr(trim($value), 1, -1), ['""' => '"', '\\"' => '"']);

			} else {
				throw CompositeTypeException::malformedInput($input);
			}
		}
		return [$values, $position];
	}


	/**
	 * @param bool $value
	 * @return string
	 */
	protected static function escapeBool($value)
	{
		return $value ? 'true' : 'false';
	}


	/**
	 * @param string $value
	 * @return string
	 */
	protected static function escapeString($value)
	{


		return '"' . str_replace('"', '""', $value) . '"';
	}

}
