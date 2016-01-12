<?php

namespace Mikulas\OrmExt;


class StateDefinitionException extends \InvalidArgumentException implements Exception
{

	/**
	 * @param string     $propertyName
	 * @param string     $message
	 * @param \Exception $previous
	 * @internal
	 */
	public function __construct($propertyName, $message, \Exception $previous = NULL)
	{
		parent::__construct($message, NULL, $previous);
	}


	/**
	 * @param \Exception $previous
	 * @return InvalidPropertyException
	 */
	public static function createNoInitialState(\Exception $previous = NULL)
	{
		return new self("State diagram does not have any state marked as initial", $previous);
	}


	/**
	 * @param string[]   $states
	 * @param \Exception $previous
	 * @return InvalidPropertyException
	 */
	public static function createMultipleInitialStates(array $states, \Exception $previous = NULL)
	{
		$statesFmt = implode("', '", $states);
		return new self("State diagram has multiple states marked as initial: '$statesFmt'", $previous);
	}


	/**
	 * @param \Exception|NULL $previous
	 * @return StateDefinitionException
	 */
	public static function createDefaultValueIgnored(\Exception $previous = NULL)
	{
		return new self("Specifying default value on StatefulProperty is not allowed, mark initial state in state diagram as StateInterface::TYPE_INITIAL instead", $previous);
	}

}
