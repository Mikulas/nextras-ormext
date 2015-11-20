<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\MappingFactory;
use Mikulas\OrmExt\Pg\SelfUpdatingPropertyMapper;


class PersonsMapper extends SelfUpdatingPropertyMapper
{

	protected function createStorageReflection()
	{
		$factory = new MappingFactory(parent::createStorageReflection());
		$factory->addJsonMapping('content');

		return $factory->getReflection();
	}

}
