<?php

namespace Mikulas\OrmExt;


class InvalidPropertyException extends \InvalidArgumentException implements Exception
{

	/** @var string */
	private $propertyName;


	/**
	 * @param string     $propertyName
	 * @param string     $message
	 * @param \Exception $previous
	 * @internal
	 */
	public function __construct($propertyName, $message, \Exception $previous = NULL)
	{
		$this->propertyName = $propertyName;
		parent::__construct($message, NULL, $previous);
	}


	/**
	 * @param string     $propertyName
	 * @param \Exception $previous
	 * @return InvalidPropertyException
	 */
	public static function createNonexistentProperty($propertyName, \Exception $previous = NULL)
	{
		return new self($propertyName, "Property '$propertyName' does not exist", $previous);
	}

}
