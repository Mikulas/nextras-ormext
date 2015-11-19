<?php

/**
 * @testCase
 */

namespace Mikulas\OrmExt\Tests\Documentation;

use Mockery;
use NextrasTests\Orm\TestCase;
use Tester\Assert;
use Tester\Environment;

$dic = require_once __DIR__ . '/../../../bootstrap.php';


class JsonExampleTest extends TestCase
{

	public function testSerializeNested()
	{
		Environment::skip('TODO implement');
	}

}


(new JsonExampleTest($dic))->run();
