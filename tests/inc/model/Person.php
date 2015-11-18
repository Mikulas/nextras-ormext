<?php

namespace Mikulas\OrmExt\Tests;

use Mikulas\OrmExt\Pg\CompositeTypePropertyProxy as Proxy;
use Nextras\Orm\Entity\Entity;


/**
 * @property int      $id       {primary}
 * @property Location $location {container Proxy}
 */
class Person extends Entity
{

}
