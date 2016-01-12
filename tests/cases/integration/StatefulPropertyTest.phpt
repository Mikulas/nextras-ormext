<?php

namespace Mikulas\OrmExt\Tests\Integration;

use Finite\Exception\StateException;
use Mikulas\OrmExt\Tests\MaritalStatus;
use Mikulas\OrmExt\Tests\Person;
use Mikulas\OrmExt\Tests\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class StatefulPropertyTest extends TestCase
{

	private static function assertConsistency(Person $person)
	{
		Assert::same(
			$person->maritalStatus->getFiniteState(),
			$person->maritalStatus->getStateMachine()->getCurrentState()->getName()
		);
	}


	public static function assertState($state, Person $person)
	{
		self::assertConsistency($person);
		Assert::same($state, $person->maritalStatus->getFiniteState());
	}


	public function testMachine()
	{
		$person = new Person();
		Assert::type(MaritalStatus::class, $person->maritalStatus);
		self::assertState(MaritalStatus::SINGLE, $person);

		Assert::true($person->maritalStatus->can(MaritalStatus::TR_MARRY));
		Assert::false($person->maritalStatus->can(MaritalStatus::TR_WIDOW));

		Assert::exception(function() use ($person) {
			$person->maritalStatus->apply(MaritalStatus::TR_WIDOW);
		}, StateException::class);
		self::assertState(MaritalStatus::SINGLE, $person);

		$person->maritalStatus->apply(MaritalStatus::TR_MARRY);
		self::assertState(MaritalStatus::MARRIED, $person);

		Assert::exception(function() use ($person) {
			$person->maritalStatus->setFiniteState('bogus');
		}, StateException::class);
		self::assertState(MaritalStatus::MARRIED, $person);
	}

}

(new StatefulPropertyTest($dic))->run();
