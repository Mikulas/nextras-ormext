<?php

namespace Mikulas\Orm;

use Mikulas\Orm\PgExt\PgArray;
use Nextras\Orm\Mapper\Dbal\StorageReflection\StorageReflection;


class MappingFactory
{

	/** @var StorageReflection */
	private $reflection;


	public function __construct(StorageReflection $reflection)
	{
		$this->reflection = $reflection;
	}


	/**
	 * @param string            $propertyName
	 * @param callable          $toEntityTransform
	 * @param callable          $toSqlTransform
	 */
	public function addGenericArrayMapping($propertyName, callable $toEntityTransform, callable $toSqlTransform)
	{
		$this->reflection->addMapping(
			$propertyName,
			$this->reflection->convertEntityToStorageKey($propertyName),
			function ($value) use ($toEntityTransform) {
				return PgArray::parse($value, $toEntityTransform);
			},
			function ($value) use ($toSqlTransform) {
				return PgArray::serialize($value, $toSqlTransform);
			}
		);
	}


	/**
	 * @param string            $propertyName
	 */
	public function addStringArrayMapping($propertyName)
	{
		$toEntity = function($partial) {
			return (string) $partial;
		};
		$toSql = function($partial) {
			return "'$partial'";
		};

		$this->addGenericArrayMapping($propertyName, $toEntity, $toSql);
	}


	/**
	 * @return StorageReflection
	 */
	public function getReflection()
	{
		return $this->reflection;
	}

}
