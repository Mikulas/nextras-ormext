<?php

/**
 * @testCase
 */

namespace Mikulas\OrmExt\Tests\Documentation;

use Mockery;
use NextrasTests\Orm\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../../bootstrap.php';


class JsonExampleTest extends TestCase
{

	public function testSerializeNested()
	{
		Assert::false('TODO');
	}

}


(new JsonExampleTest($dic))->run();
