<?php

namespace Mikulas\OrmExt;


interface IPropertyDataStore
{

	/**
	 * @param callable $listener
	 * @return void
	 */
	public function addOnModifiedListener(callable $listener);


	/**
	 * @param string $serialized
	 * @return NULL|static
	 */
	public static function parse($serialized);


	/**
	 * @return string
	 */
	public function serialize();

}
