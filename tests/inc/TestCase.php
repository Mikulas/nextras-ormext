<?php

namespace NextrasTests\Orm;

use Mikulas\OrmExt\Tests\Model;
use Mockery;
use Nette\DI\Container;
use Nextras\Orm\Model\IModel;
use Nextras\Orm\TestHelper\TestCaseEntityTrait;
use Tester;


class TestCase extends Tester\TestCase
{

	use TestCaseEntityTrait;

	/** @var Container */
	protected $container;

	/** @var Model */
	protected $orm;


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	protected function setUp()
	{
		parent::setUp();
		$this->orm = $this->container->getByType(IModel::class);
	}


	protected function tearDown()
	{
		parent::tearDown();
		Mockery::close();
	}

}
