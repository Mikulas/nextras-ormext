<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\ModifiableDataStore;
use Mikulas\OrmExt\Pg\CompositeType;
use Mikulas\OrmExt\Pg\CompositeTypeException;


class Location extends ModifiableDataStore
{

	/** @var string */
	protected $street;

	/** @var int */
	protected $houseNumber;


	/**
	 * @param string $street
	 * @param int    $houseNumber
	 */
	public function __construct($street, $houseNumber)
	{
		$this->street = $street;
		$this->houseNumber = $houseNumber;
	}


	/**
	 * @return string
	 */
	public function getStreet()
	{
		return $this->street;
	}


	/**
	 * @param string $street
	 */
	public function setStreet($street)
	{
		if ($this->street !== (string) $street) {
			$this->street = (string) $street;
			$this->onModify();
		}
	}


	/**
	 * @return int
	 */
	public function getHouseNumber()
	{
		return $this->houseNumber;
	}


	/**
	 * @param int $houseNumber
	 */
	public function setHouseNumber($houseNumber)
	{
		if (!is_int($houseNumber) && !ctype_digit($houseNumber)) {
			throw new \InvalidArgumentException;
		}

		if ($this->houseNumber !== (int) $houseNumber) {
			$this->houseNumber = (int) $houseNumber;
			$this->onModify();
		}
	}


	/**
	 * @param string $serialized
	 * @return NULL|Location
	 * @throws CompositeTypeException
	 */
	public static function parse($serialized)
	{
		if ($serialized === NULL) {
			return NULL;
		}

		list($street, $houseNumber) = CompositeType::parse($serialized);
		return new self($street, $houseNumber);
	}


	/**
	 * @return string
	 */
	public function serialize()
	{
		return CompositeType::serialize([$this->street, $this->houseNumber]);
	}

}
