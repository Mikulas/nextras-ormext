<?php

namespace Mikulas\OrmExt\Pg;

use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Mapper\Mapper;


/**
 * Requires pgsql
 */
abstract class SelfUpdatingPropertyMapper extends Mapper
{

	/**
	 * Lists properties that should be reloaded from database after persist.
	 *
	 * @return string[] property names
	 */
	protected function getSelfUpdatingProperties()
	{
		return [];
	}


	/**
	 * Default 1:1 column map
	 * @return array nextras/dbal %ex syntax
	 */
	protected function getReturningClause()
	{
		$properties = $this->getSelfUpdatingProperties();
		if (!$properties) {
			return [];
		}
		$ref = $this->getStorageReflection();
		$clause = ['RETURNING '];
		$columns = [];
		foreach ($properties as $col) {
			$columns[] .= '%column';
			$clause[] = $ref->convertEntityToStorageKey($col);
		}
		$clause[0] .= implode(', ', $columns);
		return $clause;
	}


	public function persist(IEntity $entity)
	{
		$this->beginTransaction();
		$data = $this->entityToArray($entity);
		$data = $this->getStorageReflection()->convertEntityToStorage($data);

		if (!$entity->isPersisted()) {
			$result = $this->connection->query('INSERT INTO %table %values %ex', $this->getTableName(), $data, $this->getReturningClause());
			$id = $entity->hasValue('id')
				? $entity->getValue('id')
				: $this->connection->getLastInsertedId($this->getStorageReflection()->getPrimarySequenceName());
		} else {
			$primary = [];
			$id = (array) $entity->getPersistedId();
			foreach ($this->getStorageReflection()->getStoragePrimaryKey() as $key) {
				$primary[$key] = array_shift($id);
			}
			$result = $this->connection->query('UPDATE %table SET %set WHERE %and %ex', $this->getTableName(), $data, $primary, $this->getReturningClause());
			$id = $entity->getPersistedId();
		}

		$row = $result->fetch();
		if ($row) {
			$data = [];
			foreach ($row->toArray() + ['id' => $id] as $column => $value) {
				$data[$this->getStorageReflection()->convertStorageToEntityKey($column)] = $value;
			}
			$entity->fireEvent('onLoad', [$data]);
		}
		return $id;
	}

}
