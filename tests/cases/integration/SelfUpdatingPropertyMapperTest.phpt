<?php

namespace Mikulas\OrmExt\Tests\Integration;

use Mikulas\OrmExt\Tests\Person;
use Mikulas\OrmExt\Tests\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class SelfUpdatingPropertyMapperTest extends TestCase
{

	public function testWithRealDb()
	{
		/** @var Person $person */
		$person = new Person();

		Assert::null($person->largestFavoriteNumber);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->largestFavoriteNumber);

		$person->favoriteNumbers = $exp = [-10, 2, 3, 99];
		Assert::null($person->largestFavoriteNumber);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::same(99, $person->largestFavoriteNumber);

		$person->favoriteNumbers = $exp = [-10, 2, 3];
		Assert::same(99, $person->largestFavoriteNumber);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::same(3, $person->largestFavoriteNumber);
	}

}

(new SelfUpdatingPropertyMapperTest($dic))->run();
