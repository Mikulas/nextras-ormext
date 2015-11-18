<?php

namespace NextrasTests\Orm;

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


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	protected function tearDown()
	{
		parent::tearDown();
		Mockery::close();
	}

}
