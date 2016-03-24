<?php

namespace Mikulas\OrmExt;

use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Entity\IPropertyContainer;
use Nextras\Orm\Entity\Reflection\PropertyMetadata;


abstract class PropertyProxy implements IPropertyContainer
{

	/** @var callable */
	private $setModified;

	/** @var IPropertyDataStore */
	protected $dataStore;


	/**
	 * @param IEntity          $entity
	 * @param PropertyMetadata $propertyMetadata
	 */
	public function __construct(IEntity $entity, PropertyMetadata $propertyMetadata)
	{
		$this->setModified = function() use ($entity, $propertyMetadata) {
			$entity->setAsModified($propertyMetadata->name);
		};
	}


	/**
	 * Propagate modification of injected value to entity
	 * @return void
	 */
	protected function onModify()
	{
		call_user_func($this->setModified);
	}


	/**
	 * @param mixed $serialized
	 */
	abstract public function setRawValue($serialized);


	/**
	 * Raw value is normalized value which is suitable unique identification and storing.
	 *
	 * @return mixed
	 */
	abstract public function getRawValue();


	/**
	 * @internal
	 * @param NULL|IPropertyDataStore $store
	 */
	public function setInjectedValue($store)
	{
		$this->setDataStore($store);
		$this->onModify();
	}


	/**
	 * @internal
	 * @return IPropertyDataStore
	 */
	public function & getInjectedValue()
	{
		return $this->dataStore;
	}


	/**
	 * @return bool
	 */
	public function hasInjectedValue()
	{
		return $this->dataStore !== NULL;
	}


	/**
	 * @internal
	 * @param NULL|IPropertyDataStore $store
	 */
	protected function setDataStore($store)
	{
		assert($store === NULL || $store instanceof IPropertyDataStore);
		if ($store instanceof IPropertyDataStore) {
			$store->addOnModifiedListener(function() {
				$this->onModify();
			});
		}

		$this->dataStore = $store;
	}

}
