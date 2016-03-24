<?php

namespace Mikulas\OrmExt\Tests;

use Mockery;
use Nette\DI\Container;
use Nextras\Dbal\Connection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Model\IModel;
use Nextras\Orm\Repository\IRepository;
use Nextras\Orm\TestHelper\TestCaseEntityTrait;
use Tester;


abstract class TestCase extends Tester\TestCase
{

	use TestCaseEntityTrait;

	/** @var Model */
	public $orm;

	/** @var Connection */
	public $db;

	/** @var Container */
	protected $container;

	/** @var string */
	protected $section;


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	protected function setUp()
	{
		parent::setUp();
		$this->orm = $this->container->getByType(IModel::class);
		$this->db = $this->container->getByType(Connection::class);
		Tester\Environment::lock("integration-pgsql", TEMP_DIR);
	}


	protected function tearDown()
	{
		parent::tearDown();
		Mockery::close();
	}


	/**
	 * @param IRepository $repo
	 * @param IEntity     $entity
	 * @return IEntity
	 */
	protected function persistPurgeAndLoad(IRepository $repo, IEntity $entity)
	{
		$repo->persistAndFlush($entity);
		$id = $entity->getPersistedId();

		$repo->getModel()->clearIdentityMapAndCaches(IModel::I_KNOW_WHAT_I_AM_DOING);

		return $repo->getById($id);
	}

}
