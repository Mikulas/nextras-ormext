<?php

namespace Mikulas\OrmExt;

use Finite\Exception\NoSuchPropertyException;
use Finite\State\Accessor\StateAccessorInterface;


class StatefulPropertyStateAccessor implements StateAccessorInterface
{

	/**
	 * Retrieves the current state from the given object.
	 *
	 * @param StatefulProperty $object
	 *
	 * @throws NoSuchPropertyException
	 *
	 * @return string
	 */
	public function getState($object)
	{
		assert($object instanceof StatefulProperty);
		return $object->getFiniteState();
	}


	/**
	 * Set the state of the object to the given property path.
	 *
	 * @param StatefulProperty $object
	 * @param string $value
	 *
	 * @throws NoSuchPropertyException
	 */
	public function setState(&$object, $value)
	{
		assert($object instanceof StatefulProperty);
		$object->setFiniteState($value);
	}
}
