<?php

namespace Mikulas\OrmExt;


abstract class ModifiableDataStore implements IPropertyDataStore
{

	/** @var callable[] */
	private $listeners = [];


	/**
	 * @param callable $listener
	 * @return void
	 */
	public function addOnModifiedListener(callable $listener)
	{
		$this->listeners[] = $listener;
	}


	/**
	 * Propagate modification of injected value to property proxy
	 * @return void
	 */
	protected function onModify()
	{
		foreach ($this->listeners as $listener) {
			$listener();
		}
	}

}
