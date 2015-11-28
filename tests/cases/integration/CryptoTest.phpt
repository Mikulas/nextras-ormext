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

		Assert::null($person->creditCardNumber);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->creditCardNumber);

		$person->creditCardNumber = $ccn = '123400001234000';
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::same($ccn, $person->creditCardNumber);

		$encrypted = $this->db->query('SELECT "creditCardNumber_encrypted" AS "rawField" FROM persons WHERE id = %i', $person->id)
			->fetch()->rawField;
		Assert::notSame($ccn, $encrypted);

		$person->creditCardNumber = NULL;
		Assert::null($person->creditCardNumber);
		$person = $this->persistPurgeAndLoad($this->orm->persons, $person);
		Assert::null($person->creditCardNumber);
	}

}

(new CryptoTest($dic))->run();
