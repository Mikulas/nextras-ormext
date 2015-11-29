<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\InvalidPropertyException;
use Mikulas\OrmExt\MappingFactory;
use Mockery;
use Nextras\Orm\Entity\Reflection\EntityMetadata;
use Nextras\Orm\InvalidArgumentException;
use Nextras\Orm\Mapper\Dbal\StorageReflection\StorageReflection;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class MappingFactoryTest extends TestCase
{

	public function testInvalidPropertyThrows()
	{
		/** @var Mockery\MockInterface|StorageReflection $storageReflection */
		$storageReflection = Mockery::mock(StorageReflection::class);
		$storageReflection->shouldReceive('convertEntityToStorageKey')
			->andReturnSelf();
		$storageReflection->shouldReceive('addMapping')
			->andReturn();

		/** @var Mockery\MockInterface|EntityMetadata $entityMetadata */
		$entityMetadata = Mockery::mock(EntityMetadata::class);
		$entityMetadata->shouldReceive('getProperty')
			->andReturnUsing(function($prop) {
				if ($prop !== 'exists') {
					throw new InvalidArgumentException();
				}
			});

		$factory = new MappingFactory($storageReflection, $entityMetadata);

		Assert::exception(function() use ($factory) {
			$factory->addIntArrayMapping('unknown');
		}, InvalidPropertyException::class);

		$factory->addIntArrayMapping('exists');
	}

}

(new MappingFactoryTest($dic))->run();
