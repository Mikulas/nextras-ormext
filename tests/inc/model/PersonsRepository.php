<?php

namespace Mikulas\OrmExt\Tests;

use Nextras\Orm\Repository\Repository;


/**
 * @method Person getById($id)
 */
class PersonsRepository extends Repository
{

	/**
	 * @return string[]
	 */
	public static function getEntityClassNames()
	{
		return [Person::class];
	}

}
