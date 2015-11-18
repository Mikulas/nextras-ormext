# Features

- [`Json`](#Json)
- [`PgArray`](#PgArray)
- [`CompositeType`](#CompositeType)
- [`MappingFactory`](#MappingFactory)

## `Json`

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


## `PgArray`

Adds support for [PostgreSQL array types](http://www.postgresql.org/docs/9.4/static/arrays.html)

Depending on your desired behaviour, `null` can either be cast to empty array, or be left as `null`.

Can be used both as `@property type[]` and `@property NULL|type[]`, where `type` is anything your transformation functions
will handle. Transformation functions are arguments to `PgArray::parse` and `PgArray::serialize` and are also called for `null` values.

`MappingFactory` helper contains `addStringArrayMapping` and `addGenericArrayMapping` that provide a nice level of abstraction.

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



### `CompositeType` Example

```sql
CREATE TYPE latlng AS (
	lat numeric(14, 11),
	lng numeric(14, 11)
);
CREATE TYPE location AS (
	coords latlng,
	name text
);
CREATE TABLE persons (
	location location NOT NULL
);
```

```php
/**
 * @property array $location
 */
class Person {}
```

TODO

```php
class PersonsMapper extends Mapper
{

}
```

## `MappingFactory`

`StorageReflection` decorator. Simplifies mapping definitions.

### `MappingFactory` Example

see [`Json` Example](#Json-Example)
