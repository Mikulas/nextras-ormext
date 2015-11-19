# Features

- [`Json`](#json)
- [`Crypto`](#crypto)
- [`PgArray`](#pgarray)
- [`CompositeType`](#compositetype)
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
		$factory = new MappingFactory(parent::createStorageReflection());
		$factory->addJsonMapping('content');

		return $factory->getReflection();
	}

}
```

```php
$notes->content['abstract'] = 'Lorem ipsum...';
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
		$factory = new MappingFactory(parent::createStorageReflection());
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

`MappingFactory` helper contains `addStringArrayMapping` and `addGenericArrayMapping` that provide a nice level of abstraction.

- [`PgArray`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.Pg.PgArray.html)
- [`MappingFactory::addStringArrayMapping`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html#_addStringArrayMapping)
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
		$factory = new MappingFactory(parent::createStorageReflection());
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
class Person {}
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

## `MappingFactory`

`StorageReflection` decorator. Simplifies mapping definitions.

- [`MappingFactory`](https://codedoc.pub/Mikulas/nextras-ormext/master/class-Mikulas.OrmExt.MappingFactory.html)

### `MappingFactory` Example

See [`Json` Example](#json-example) and other snippets on this page.
