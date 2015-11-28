<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\Pg\CompositeTypePropertyProxy as Proxy;
use Nextras\Orm\Entity\Entity;


/**
 * @property      int            $id       {primary}
 * @property      NULL|Location  $location {container Proxy}
 * @property-read NULL|mixed     $content
 */
class Person extends Entity
{

	/**
	 * Prevent setting via array access, which would
	 * not invoke onModify
	 *
	 * @param mixed|NULL $content
	 */
	public function setContent(array $content = NULL)
	{
		$this->setReadOnlyValue('content', $content);
	}

}
