<?php

namespace Mikulas\OrmExt\Tests\Documentation;

use Mikulas\OrmExt\Tests\Person;
use Mikulas\OrmExt\Tests\TestCase;
use Mockery;
use Nextras\Orm\Model\IModel;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../../bootstrap.php';


class JsonExampleTest extends TestCase
{

	public function testSerializeNested()
	{
		$person = new Person();
		Assert::null($person->content);$this->orm->persons->persistAndFlush($person);

		Assert::false($person->isModified('content'));
		$person->setContent(['abstract' => 'Lorem ipsum']);
		Assert::true($person->isModified('content'));

		$this->orm->persons->persistAndFlush($person);
		Assert::false($person->isModified('content'));

		$personId = $person->getPersistedId();
		$this->orm->clearIdentityMapAndCaches(IModel::I_KNOW_WHAT_I_AM_DOING);
		$person = $this->orm->persons->getById($personId);

		Assert::same(['abstract' => 'Lorem ipsum'], $person->content);
	}

}


(new JsonExampleTest($dic))->run();
