# Features

## `PgArray`

Adds support for [PostgreSQL array types](http://www.postgresql.org/docs/9.4/static/arrays.html)

Depending on your desired behaviour, `null` can either be cast to empty array, or be left as `null`.

Can be used both as `@property type[]` and `@property NULL|type[]`, where `type` is anything your transformation functions
will handle. Transformation functions are arguments to `PgArray::parse` and `PgArray::serialize` and are also called for `null` values.

### `PgArray` Example

```sql
CREATE TABLE "book" (
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
		$reflection = parent::createStorageReflection();
		$reflection->addMapping(
			'authors',
			'authors',
			function ($value) {
				return PgArray::parse($value, function($author) {return (string} $author);
			},
			function ($value) use ($pgArray) {
				return PgArray::serialize($value, function($author) {return (string} $author);
			}
		);

		return $reflection;
	}

}
```
