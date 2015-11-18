<?php

/**
 * @testCase
 */

namespace Mikulas\OrmExt\Pg;

use Mockery;
use NextrasTests\Orm\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../../bootstrap.php';


class CompositeTypeTest extends TestCase
{

	/**
	 * @return array [$sql, $php]
	 */
	public function provideCases()
	{
		return [
			['()', [NULL]],
			['(pre,)', ['pre', NULL]],
			['(,post)', [NULL, 'post']],
			['(,)', [NULL, NULL]],

			['(a)', ['a']],

			['("q""o")', ['q"o']],
			['("par)en")', ['par)en']],

			['( chars )', [' chars ']],
			['(42)', [42]],

			['((lat,lng),radius)', [['lat', 'lng'], 'radius']],
		];
	}


	/**
	 * @dataProvider provideCases
	 */
	public function testParse($sql, $php)
	{
		Assert::same($php, CompositeType::parse($sql));
	}

	/**
	 * @dataProvider provideCases
	 */
	public function testSerialize($sql, $php)
	{
		Assert::same($sql, CompositeType::serialize($php));
	}

}


(new CompositeTypeTest($dic))->run();
