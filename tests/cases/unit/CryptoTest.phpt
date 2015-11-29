<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\Crypto;
use Mockery;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class CompositeTypeTest extends TestCase
{

	public function testAll()
	{
		$cryptoA = new Crypto('aaccca440ab7791d3c9591e5a0eb80db');
		$cryptoB = new Crypto('991cdb7e62a7193b368245fc874b00c8');

		$plainInput = 'ahoj';
		$garbleFromA = $cryptoA->encrypt($plainInput);
		Assert::notSame($garbleFromA, $plainInput);

		$garbleFromB = $cryptoB->encrypt($plainInput);
		Assert::notSame($garbleFromB, $plainInput);
		Assert::notSame($garbleFromB, $garbleFromA);

		$plainOutput = $cryptoA->decrypt($garbleFromA);
		Assert::same($plainOutput, $plainInput);

		$plainOutput = $cryptoB->decrypt($garbleFromB);
		Assert::same($plainOutput, $plainInput);
	}

}

(new CompositeTypeTest($dic))->run();
