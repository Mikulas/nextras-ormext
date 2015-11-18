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
		parent::__construct($message, $previous);
	}


	public static function tokenizerFailure(TokenizerException $e)
	{
		return new self('Failed during tokenization', $e);
	}


	public static function openFailed()
	{
		return new self("Invalid T_OPEN as first token");
	}


	public static function mismatchedBrackets()
	{
		return new self('Expected T_CLOSE as last token');
	}

}
