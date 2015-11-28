<?php

namespace Mikulas\OrmExt\Tests\Documentation\CompositeType;

use Mikulas\OrmExt\Tests\Location;
use Mikulas\OrmExt\Tests\Person;
use Mikulas\OrmExt\Tests\TestCase;
use Mockery;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../../../bootstrap.php';


class CompositeTypeTest extends TestCase
{

	public function testFlow()
	{
		$person = new Person();
		Assert::null($person->location);
		$this->orm->persons->persist($person);
		Assert::false($person->isModified('location'));

		$person->location = new Location('Foo', 12);
		Assert::true($person->isModified('location'));

		$this->orm->persons->persist($person);
		Assert::false($person->isModified('location'));
		$person->location->setHouseNumber(12);
		Assert::false($person->isModified('location'));
		$person->location->setHouseNumber(1173);
		Assert::true($person->isModified('location'));
	}

}


(new CompositeTypeTest($dic))->run();
