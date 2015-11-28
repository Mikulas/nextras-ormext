<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\MappingFactory;


class PersonsMapper extends Mapper
{

	protected function createStorageReflection()
	{
		$factory = new MappingFactory(parent::createStorageReflection());
		$factory->addJsonMapping('content');

		return $factory->getReflection();
	}

}
