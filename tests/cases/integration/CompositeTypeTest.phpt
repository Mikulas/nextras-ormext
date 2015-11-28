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
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::same($person->location->getStreet(), 'street');
		Assert::same($person->location->getHouseNumber(), 12);

		$person->location = NULL;
		Assert::null($person->location);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->location);
	}

}

(new CompositeTypeTest($dic))->run();
