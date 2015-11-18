<?php

namespace Mikulas\Orm\PgExt;

use Nette\Utils\TokenizerException;


class PgArrayException extends \InvalidArgumentException implements Exception
{

	/**
	 * @internal
	 */
	public function __construct($message, \Exception $previous = NULL)
	{
		parent::__construct($message, NULL, $previous);
	}


	public static function tokenizerFailure(TokenizerException $e)
	{
		return self::malformedInput('Failed during tokenization.');
	}


	public static function openFailed()
	{
		return self::malformedInput("Expected '{' as first token.");
	}


	public static function mismatchedBrackets()
	{
		return self::malformedInput("Expected '}' as last token.");
	}


	public static function malformedInput($reason = '')
	{
		return new self("Malformed input, expected recursive '{ val1 delim val2 delim ... }' syntax." . ($reason ? " $reason" : ''));
	}

}
