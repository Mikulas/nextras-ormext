<?php

namespace Mikulas\OrmExt;

use Mikulas\OrmExt\Pg\PgArray;
use Nette\Utils\Json;
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
	 * @param string $propertyName
	 */
	public function addJsonMapping($propertyName)
	{
		$this->reflection->addMapping(
			$propertyName,
			$this->reflection->convertEntityToStorageKey($propertyName),
			function ($value) {
				return Json::decode($value);
			},
			function ($value) {
				return Json::encode($value);
			}
		);
	}


	/**
	 * @param string $propertyName
	 * @param Crypto $crypto
	 * @param string $sqlPostfix
	 */
	public function addCryptoMapping($propertyName, Crypto $crypto, $sqlPostfix = '_encrypted')
	{
		$this->reflection->addMapping(
			$propertyName,
			$this->reflection->convertEntityToStorageKey($propertyName) . $sqlPostfix,
			function ($garble) use ($crypto) {
				return $garble === NULL ? NULL : $crypto->decrypt($garble);
			},
			function ($plain) use ($crypto) {
				return $plain === NULL ? NULL : $crypto->encrypt($plain);
			}
		);
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
