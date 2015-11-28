<?php

namespace Mikulas\OrmExt\Tests\Integration;

use Mikulas\OrmExt\Tests\Person;
use Mikulas\OrmExt\Tests\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class CryptoTest extends TestCase
{

	public function testWithRealDb()
	{
		/** @var Person $person */
		$person = new Person();

		Assert::null($person->content);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->content);

		$exp = [
			'foo' => 'bar',
			'spike' => [1, 2, 3],
			'bar' => [
				['type' => 1],
				['type' => 2],
				['type' => 3],
			],
		];
		$person->setContent($exp);
		Assert::same($exp, $person->content);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::same($exp, $person->content);

		$person->setContent(NULL);
		Assert::null($person->content);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->content);
	}

}

(new CryptoTest($dic))->run();
