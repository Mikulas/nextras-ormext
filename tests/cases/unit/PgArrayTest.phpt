<?php

/**
 * @testCase
 */

namespace Mikulas\Orm\PgExt;

use DateTimeImmutable;
use DateTimeZone;
use Mockery;
use NextrasTests\Orm\TestCase;
use Tester\Assert;

$dic = require_once __DIR__ . '/../../bootstrap.php';


class PostgresArrayTest extends TestCase
{

	public function testParseString()
	{
		$toString = function($partial) {
			return $partial === NULL ? NULL : (string) $partial;
		};

		Assert::same(NULL, PgArray::parse(NULL, $toString));
		Assert::same([], PgArray::parse('{}', $toString));
		Assert::same(['a', 'b'], PgArray::parse('{"a", "b"}', $toString));
	}


	public function testParseDateTime()
	{
		$toDate = function($partial) {
			return $partial === NULL ? NULL : new DateTimeImmutable($partial);
		};

		/** @var DateTimeImmutable[] $parsed */
		$parsed = PgArray::parse('{"2015-01-01 10:11:12","2015-02-02 12:13:14"}', $toDate);

		Assert::count(2, $parsed);
		Assert::type(DateTimeImmutable::class, $parsed[0]);
		Assert::type(DateTimeImmutable::class, $parsed[1]);
		Assert::same('2015-01-01 10:11:12', $parsed[0]->format('Y-m-d H:i:s'));
		Assert::same('2015-02-02 12:13:14', $parsed[1]->format('Y-m-d H:i:s'));
	}


	public function testParseNested()
	{
		$toNumber = function($partial) {
			return $partial === NULL ? NULL : (int) $partial;
		};

		Assert::same([[1, 2], [3, 4, 5]], PgArray::parse('{{1,2},{3,4,5}}', $toNumber));
	}


	public function testSerializeString()
	{
		$fromString = function($partial) {
			return $partial === NULL ? NULL : "'$partial'";
		};

		Assert::same(NULL, PgArray::serialize(NULL, $fromString));
		Assert::same('{}', PgArray::serialize([], $fromString));
		Assert::same("{'a','b'}", PgArray::serialize(['a', 'b'], $fromString));
	}


	public function testSerializeDate()
	{
		$fromDate = function(DateTimeImmutable $partial) {
			if ($partial === NULL) {
				return NULL;
			}
			$normalized = $partial->setTimezone(new DateTimeZone(date_default_timezone_get()));
			return "'" . $normalized->format('Y-m-d H:i:s') . "'";
		};

		$dates = [
			new DateTimeImmutable('2015-01-01 10:11:12'),
			new DateTimeImmutable('2015-02-02 12:13:14'),
		];
		Assert::same("{'2015-01-01 10:11:12','2015-02-02 12:13:14'}", PgArray::serialize($dates, $fromDate));
	}


	public function testSerializeNested()
	{
		$fromNumber = function($partial) {
			return $partial === NULL ? NULL : (int) $partial;
		};

		Assert::same('{{1,2},{3,4,5}}', PgArray::serialize([[1, 2], [3, 4, 5]], $fromNumber));

		Assert::same(NULL, PgArray::serialize([], $fromNumber, TRUE));
		Assert::same('{}', PgArray::serialize([], $fromNumber, FALSE));
	}

}


(new PostgresArrayTest($dic))->run();
