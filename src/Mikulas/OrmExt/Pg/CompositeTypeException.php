<?php

namespace Mikulas\OrmExt\Pg;

use Mikulas\OrmExt\Exception;
use Nette\Utils\TokenizerException;


class CompositeTypeException extends \InvalidArgumentException implements Exception
{

	/** @var string */
	private $input;


	/**
	 * @internal
	 * @param string     $input
	 * @param string     $message
	 * @param \Exception $previous
	 */
	public function __construct($input, $message, \Exception $previous = NULL)
	{
		$this->input = $input;

		$message .= "\nInput: '$input'";
		parent::__construct($message, NULL, $previous);
	}


	/**
	 * @param string             $input
	 * @param TokenizerException $e
	 * @return PgArrayException
	 */
	public static function tokenizerFailure($input, TokenizerException $e)
	{
		return self::malformedInput($input, 'Failed during tokenization.', $e);
	}


	/**
	 * @param string          $input
	 * @param \Exception|NULL $previous
	 * @return PgArrayException
	 */
	public static function openFailed($input, \Exception $previous = NULL)
	{
		return self::malformedInput($input, "Expected '(' as first token.", $previous);
	}


	/**
	 * @param string          $input
	 * @param \Exception|NULL $previous
	 * @return PgArrayException
	 */
	public static function mismatchedParens($input, \Exception $previous = NULL)
	{
		return self::malformedInput($input, "Expected ')' as last token.", $previous);
	}


	/**
	 * @param string          $input
	 * @param string          $reason
	 * @param \Exception|NULL $previous
	 * @return PgArrayException
	 */
	public static function malformedInput($input, $reason = '', \Exception $previous = NULL)
	{
		return new self($input, "Malformed input, expected recursive '( val1 , val2 , ... )' syntax."
			. ($reason ? " $reason" : ''), $previous);
	}

}
