<?php

/**
 * This file is part of the Nextras\Orm library.
 * @license    MIT
 * @link       https://github.com/nextras/orm
 */

namespace NextrasTests\Orm;

use Nextras\Orm\InvalidStateException;
use Tester\Environment;
use Tester\TestCase;


class Helper
{
	const SECTION_MYSQL = 'mysql';
	const SECTION_PGSQL = 'pgsql';
	const SECTION_ARRAY = 'array';


	public static function check()
	{
		if (!is_file(__DIR__ . '/../config.neon')) {
			throw new InvalidStateException("Missing 'tests/config.neon' configuration file.");
		}
		if (!is_file(__DIR__ . '/../php.ini')) {
			throw new InvalidStateException("Missing 'tests/php.ini' configuration file.");
		}
	}


	public static function isRunByRunner()
	{
		return getenv(Environment::RUNNER) === '1';
	}


	public static function isRunForListingMethods()
	{
		foreach ((array) $_SERVER['argv'] as $arg) {
			if ($arg === '--method=' . TestCase::LIST_METHODS) {
				return TRUE;
			}
		}
		return FALSE;
	}

}
