<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\Crypto;
use Nette\Caching\Cache;
use Nextras\Dbal\Connection;


class PersonsMapper extends Mapper
{

	/** @var Crypto */
	private $crypto;


	public function __construct(Connection $connection, Cache $cache, Crypto $crypto)
	{
		parent::__construct($connection, $cache);
		$this->crypto = $crypto;
	}


	protected function getSelfUpdatingProperties()
	{
		return ['largestFavoriteNumber'];
	}


	protected function createStorageReflection()
	{
		$factory = $this->createMappingFactory();
		$factory->addJsonMapping('content');
		$factory->addCryptoMapping('creditCardNumber', $this->crypto);
		$factory->addIntArrayMapping('favoriteNumbers');

		return $factory->getStorageReflection();
	}

}
