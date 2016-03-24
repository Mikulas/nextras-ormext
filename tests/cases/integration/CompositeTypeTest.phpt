<?php

namespace Mikulas\OrmExt\Tests\Integration;

use Mikulas\OrmExt\Tests\Location;
use Mikulas\OrmExt\Tests\Person;
use Mikulas\OrmExt\Tests\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class CompositeTypeTest extends TestCase
{

	public function testWithRealDb()
	{
		/** @var Person $person */
		$person = new Person();

		Assert::null($person->location);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->location);

		$person->location = new Location('street', 12);
		Assert::same($person->location->getStreet(), 'street');
		Assert::same($person->location->getHouseNumber(), 12);
		Assert::true($person->isModified('location'));

		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::same($person->location->getStreet(), 'street');
		Assert::same($person->location->getHouseNumber(), 12);
		Assert::false($person->isModified('location'));

		$person->location->setStreet('different street');
		Assert::true($person->isModified('location'));

		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		$person->location = new Location('yet another street', 17);
		Assert::true($person->isModified('location'));

		$person->location = NULL;
		Assert::null($person->location);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->location);
	}

}

(new CompositeTypeTest($dic))->run();
