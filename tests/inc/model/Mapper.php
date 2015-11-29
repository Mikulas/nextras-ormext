<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\MappingFactory;
use Mikulas\OrmExt\Pg\SelfUpdatingPropertyMapper;
use Nextras\Orm\Mapper\Dbal\StorageReflection\CamelCaseStorageReflection;


class Mapper extends SelfUpdatingPropertyMapper
{

	protected function createStorageReflection()
	{
		return new CamelCaseStorageReflection(
			$this->connection,
			$this->getTableName(),
			$this->getRepository()->getEntityMetadata()->getPrimaryKey(),
			$this->cacheStorage
		);
	}


	/**
	 * @return MappingFactory
	 */
	protected function createMappingFactory()
	{
		return new MappingFactory(self::createStorageReflection(), $this->getRepository()->getEntityMetadata());
	}

}
