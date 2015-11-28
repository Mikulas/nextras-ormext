<?php

namespace Mikulas\OrmExt\Tests\Integration;

use Mikulas\OrmExt\Tests\Location;
use Mikulas\OrmExt\Tests\Person;
use Mikulas\OrmExt\Tests\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class PgArrayTest extends TestCase
{

	public function testWithRealDb()
	{
		/** @var Person $person */
		$person = new Person();

		Assert::null($person->favoriteNumbers);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->favoriteNumbers);

		$person->favoriteNumbers = $exp = [-10, 2, 3, 99];
		Assert::same($exp, $person->favoriteNumbers);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::same($exp, $person->favoriteNumbers);

		$person->favoriteNumbers = NULL;
		Assert::null($person->favoriteNumbers);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->favoriteNumbers);
	}

}

(new PgArrayTest($dic))->run();
