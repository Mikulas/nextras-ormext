# Features

- [`Json`](#json)
- [`Crypto`](#crypto)
- [`PgArray`](#pgarray)
- [`CompositeType`](#compositetype)
- [`SelfUpdatingPropertyMapper`](#selfupdatingpropertymapper)
- [`StatefulProperty`](#statefulproperty)
- [`MappingFactory`](#mappingfactory)

[Api documentation](https://codedoc.pub/Mikulas/nextras-ormext/master/index.html)

## `Json`

Converts arbitrary php hash to json for storing purposes and then back to php hash. Setup is as easy as specifying
new mapping.

- [`MappingFactory::addJsonMapping`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html#_addJsonMapping)

### `Json` Example

```sql
CREATE TABLE "notes" (
	"content" JSONB NOT NULL
);
```

```php
class NotesMapper extends Mapper
{

	protected function createStorageReflection()
	{
		$factory = new MappingFactory(parent::createStorageReflection(), $this->getRepository()->getEntityMetadata());
		$factory->addJsonMapping('content');

		return $factory->getReflection();
	}

}
```

#### Usage

```php
$notes->setContent(['abstract' => 'Lorem ipsum...']);
```

#### Incorrect usage

I you wan't to use array-access setters, you have to call `setModified()` manually. We recommend creating a setter instead, as in the main example below.

> E_NOTICE: Indirect modification of overloaded property

```php
@$notes->content['abstract'] = 'Lorem ipsum...';
$notes->setAsModified('content');
```

## `Crypto`

Saves encrypted values in database and decodes them back when fetched to php. Uses AES-256 compliant Rijndael-128.

Column name is automatically appended with `_encrypted`, which you can change in `addCryptoMapping($column, Crypto, $postfix)` call.

Please note NULL values are not encrypted by default. This may present security issues (it leaks that something is or is not set).
Reasoning behind this is that column constraints could not be enforced properly if `NULL` was encrypted to `string`.

Database type for encrypted field should be `TEXT`, unbound string.

Requires `ext-mcrypt`.

- [`Crypto`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.Crypto.html)
- [`MappingFactory::addCryptoMapping`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html#_addCryptoMapping)

### `Crypto` Example

```sql
CREATE TABLE "users" (
	"email_encrypted" TEXT NOT NULL
);
```

```php
class UsersMapper extends Mapper
{

	/** @var Crypto */
	protected $crypto;


	/**
	 * @property Crypto $crypto
	 */
	public function __construct(Crypto $crypto)
	{
		$this->crypto = $crypto;
	}


	protected function createStorageReflection()
	{
		$factory = new MappingFactory(parent::createStorageReflection(), $this->getRepository()->getEntityMetadata());
		$factory->addCryptoMapping('email', $this->crypto);

		return $factory->getReflection();
	}

}
```

## `PgArray`

Adds support for [PostgreSQL array types](http://www.postgresql.org/docs/9.4/static/arrays.html)

Depending on your desired behaviour, `null` can either be cast to empty array, or be left as `null`.

Can be used both as `@property type[]` and `@property NULL|type[]`, where `type` is anything your transformation functions
will handle. Transformation functions are arguments to `PgArray::parse` and `PgArray::serialize` and are also called for `null` values.

`MappingFactory` helper contains implementations for string, integer and DateTime arrays. Additional array formats can be easily added
with `addGenericArrayMapping`.

Default implementation of `addDateTimeArrayMapping` expects dates to be normalized to zero timezone offset.

- [`PgArray`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.Pg.PgArray.html)
- [`MappingFactory::addStringArrayMapping`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html#_addStringArrayMapping)
- [`MappingFactory::addIntArrayMapping`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html#_addIntArrayMapping)
- [`MappingFactory::addDateTimeArrayMapping`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html#_addDateTimeArrayMapping)
- [`MappingFactory::addGenericArrayMapping`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html#_addGenericArrayMapping)

### `PgArray` Example

```sql
CREATE TABLE "books" (
	"authors" TEXT[] NOT NULL
);
```

```php
/**
 * @property string[] $authors
 */
class Book extends Entity {}
```

```php
class BooksMapper extends Mapper
{

	protected function createStorageReflection()
	{
		$factory = new MappingFactory(parent::createStorageReflection(), $this->getRepository()->getEntityMetadata());
		$factory->addStringArrayMapping('authors');

		return $factory->getReflection();
	}

}
```

## `CompositeType`

Parses PostgreSQL [composite types](http://www.postgresql.org/docs/current/static/rowtypes.html) into php hash.

Proxy for wrapping `CompositeType` to object is also provided: `CompositeTypePropertyProxy`. See example below.

- [`CompositeType`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.Pg.CompositeType.html)
- [`CompositeTypePropertyProxy`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.Pg.CompositeTypePropertyProxy.html)

### `CompositeType` Example

```sql
CREATE TYPE location AS (
	street TEXT,
	houseNumber INT
);
CREATE TABLE persons (
	location location NOT NULL
);
```

```php
use Mikulas\OrmExt\Pg\CompositeTypePropertyProxy as Proxy;

/**
 * @property Location $location {container Proxy}
 */
class Person extends Entity {}
```

```php
use Mikulas\OrmExt\ModifiableDataStore;
use Mikulas\OrmExt\Pg\CompositeType;
use Mikulas\OrmExt\Pg\CompositeTypeException;


class Location extends ModifiableDataStore
{

	/** @var string */
	protected $street;

	/** @var int */
	protected $houseNumber;


	/**
	 * @param string $street
	 * @param int    $houseNumber
	 */
	public function __construct($street, $houseNumber)
	{
		$this->street = $street;
		$this->houseNumber = $houseNumber;
	}


	/**
	 * @param string $street
	 */
	public function setStreet($street)
	{
		if ($this->street !== (string) $street) {
			$this->street = (string) $street;
			$this->onModify();
		}
	}


	/**
	 * @param int $houseNumber
	 */
	public function setHouseNumber($houseNumber)
	{
		if (!is_int($houseNumber) && !ctype_digit($houseNumber)) {
			throw new \InvalidArgumentException;
		}

		if ($this->houseNumber !== (int) $houseNumber) {
			$this->houseNumber = (int) $houseNumber;
			$this->onModify();
		}
	}


	/**
	 * @param string $serialized
	 * @return NULL|Location
	 * @throws CompositeTypeException
	 */
	public static function parse($serialized)
	{
		if ($serialized === NULL) {
			return NULL;
		}

		list($street, $houseNumber) = CompositeType::parse($serialized);
		return new self($street, $houseNumber);
	}


	/**
	 * @return string
	 */
	public function serialize()
	{
		return CompositeType::serialize([$this->street, $this->houseNumber]);
	}

}
```

#### Usage

```php
$person = new Person();
$person->location; // NULL

$person->location = new Location();
$person->location->setStreet('Foobar');

$repository->persist($person);
```

## `SelfUpdatingPropertyMapper`

Reloads properties after entity persist. Useful for propagating database logic back to application. For example,
when updating restaurant, before-triggers may recompute a price group ($, $$, $$$) of the restaurant info `price_group`.
This mapper simplifies loading this field back to entity for further processing.

- [SelfUpdatingPropertyMapper](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.Pg.SelfUpdatingPropertyMapper.html)

### `SelfUpdatingPropertyMapper` Example

```sql
CREATE TABLE "doughnuts" (
	"a" integer,
	"b" integer,
	"computed_property" integer
);
CREATE FUNCTION compute_property() RETURNS trigger AS $$ BEGIN
	NEW.computed_property = NEW.a * NEW.b;
	RETURN NEW;
END; $$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_compute_property BEFORE INSERT OR UPDATE ON doughnuts
FOR EACH ROW EXECUTE PROCEDURE compute_property();
```

```php
/**
 * @property      int      $a
 * @property      int      $b
 * @property-read int|NULL $computedProperty
 */
class Doughnut extends Entity {}
```

```php
class DoughnutMapper extends SelfUpdatingPropertyMapper
{

	/**
	 * Lists properties that should be reloaded from database after persist.
	 *
	 * @return string[] property names
	 */
	protected function getSelfUpdatingProperties()
	{
		return ['computedProperty'];
	}

}
```

#### Usage

```php
$doughnut = new Doughnut;
$doughnut->a = 3;
$doughnut->b = 5;
$doughnut->computedProperty; // NULL

$repo->persist($doughnut);

$doughnut->computedProperty; // 15
```

## `StatefulProperty`

Only use usable with optional dependency [`eyohang/finite`](https://github.com/yohang/Finite) with version `~1.1`.

- [`StatefulProperty`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.StatefulProperty.html)

### `StatefulProperty` Example

```php
class MaritalStatus extends StatefulProperty
{

	# states
	const SINGLE = 'single';
	const MARRIED = 'married';
	const DIVORCED = 'divorced';
	const WIDOWED = 'widowed';

	# transitions
	const TR_MARRY = 'marry';
	const TR_DIVORCE = 'divorce';
	const TR_WIDOW = 'widow';


	protected function getStates()
	{
		return [
			self::SINGLE => ['type' => StateInterface::TYPE_INITIAL],
			self::MARRIED => ['type' => StateInterface::TYPE_NORMAL],
			self::DIVORCED => ['type' => StateInterface::TYPE_NORMAL],
			self::WIDOWED => ['type' => StateInterface::TYPE_NORMAL],
		];
	}

	protected function getTransitions()
	{
		return [
			self::TR_MARRY => [
				'from' => [self::SINGLE, self::DIVORCED, self::WIDOWED],
				'to' => self::MARRIED,
			],
			self::TR_DIVORCE => [
				'from' => [self::MARRIED],
				'to' => self::DIVORCED,
			],
			self::TR_WIDOW => [
				'from' => [self::MARRIED],
				'to' => self::WIDOWED,
			],
		];
	}

}
```

```php
use Mikulas\OrmExt\Pg\CompositeTypePropertyProxy as Proxy;

/**
 * @property      int           $id                    {primary}
 * @property      MaritalStatus $maritalStatus         {container Proxy}
 */
class Person extends Entity { }
```

#### Usage

```php
$person = new Person();
$person->maritalStatus->getFiniteState(); // single
$person->maritalStatus->can('divorce'); // false, single person cannot divorce
$person->maritalStatus->can('marry'); // true, single person can marry
$person->maritalStatus->apply('marry');
$person->maritalStatus->getFiniteState(); // married
```

## `MappingFactory`

`StorageReflection` decorator. Simplifies mapping definitions.

Note that Orm does silently ignores (nonexistent) property names. All calls to `MappingFactory` validate
 property names and throw, so while it's optional, it's highly recommended to use `MappingFactory`.

- [`MappingFactory`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html)

### `MappingFactory` Example

While almost a decorator for `IMapper`, to validate property names `MappingFactory` must also be constructed with property
 metadata. Having a mapping factory factory such as this is advised:

```php
abstract class AMapper extends BaseMapper {

	/**
	 * @return MappingFactory
	 */
	protected function createMappingFactory()
	{
		return new MappingFactory(parent::createStorageReflection(), $this->getRepository()->getEntityMetadata());
	}

}
```

See [`Json` Example](#json-example) and other snippets on this page.
