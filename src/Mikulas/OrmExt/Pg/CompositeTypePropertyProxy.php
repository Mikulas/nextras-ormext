<?php

namespace Mikulas\OrmExt\Pg;

use Mikulas\OrmExt\IPropertyDataStore;
use Mikulas\OrmExt\PropertyProxy;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Entity\Reflection\PropertyMetadata;


class CompositeTypePropertyProxy extends PropertyProxy
{

	/** @var string */
	private $dataStoreClass;


	/**
	 * @param IEntity          $entity
	 * @param PropertyMetadata $propertyMetadata
	 */
	public function __construct(IEntity $entity, PropertyMetadata $propertyMetadata)
	{
		parent::__construct($entity, $propertyMetadata);

		if (count($propertyMetadata->types) > 1) {
			throw new \Exception(__CLASS__ . ' can support only one type, multiple types ' . implode('|', $propertyMetadata->types) . ' given'); // TODO
		}
		$this->dataStoreClass = array_keys($propertyMetadata->types)[0];

		if (!is_subclass_of($this->dataStoreClass, IPropertyDataStore::class, TRUE)) {
			throw new \Exception("Type '{$this->dataStoreClass}' must be an existing descendant of IPropertyDataStore"); // TODO
		}
	}


	/**
	 * @param mixed $serialized
	 */
	public function setRawValue($serialized)
	{
		/** @var IPropertyDataStore $store */
		$store = NULL;

		$store = call_user_func([$this->dataStoreClass, 'parse'], $serialized);
		if ($store !== NULL && ! $store instanceof IPropertyDataStore) {
			throw new \Exception('IPropertyDataStore::parse must return instance of IPropertyDataStore'); // TODO
		}

		$this->setDataStore($store);
	}


	/**
	 * Raw value is normalized value which is suitable unique identification and storing.
	 *
	 * @return NULL|string
	 */
	public function getRawValue()
	{
		return $this->dataStore === NULL ? NULL : $this->dataStore->serialize();
	}

}
