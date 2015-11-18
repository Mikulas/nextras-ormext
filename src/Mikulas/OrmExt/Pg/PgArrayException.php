<?php

namespace Mikulas\OrmExt\Pg;

use Mikulas\OrmExt\Exception;
use Nette\Utils\TokenizerException;


class PgArrayException extends \InvalidArgumentException implements Exception
{

	/**
	 * @internal
	 * @param string     $message
	 * @param \Exception $previous
	 */
	public function __construct($message, \Exception $previous = NULL)
	{
		parent::__construct($message, NULL, $previous);
	}


	/**
	 * @param TokenizerException $e
	 * @return PgArrayException
	 */
	public static function tokenizerFailure(TokenizerException $e)
	{
		return self::malformedInput('Failed during tokenization.', $e);
	}


	/**
	 * @param \Exception|NULL $previous
	 * @return PgArrayException
	 */
	public static function openFailed(\Exception $previous = NULL)
	{
		return self::malformedInput("Expected '{' as first token.", $previous);
	}


	/**
	 * @param \Exception|NULL $previous
	 * @return PgArrayException
	 */
	public static function mismatchedBrackets(\Exception $previous = NULL)
	{
		return self::malformedInput("Expected '}' as last token.", $previous);
	}


	/**
	 * @param string          $reason
	 * @param \Exception|NULL $previous
	 * @return PgArrayException
	 */
	public static function malformedInput($reason = '', \Exception $previous = NULL)
	{
		return new self("Malformed input, expected recursive '{ val1 delim val2 delim ... }' syntax."
			. ($reason ? " $reason" : ''), $previous);
	}

}
