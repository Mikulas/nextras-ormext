<?php

use Nette\DI\Container;
use Nette\Neon\Neon;
use Nextras\Dbal\Connection;
use Nextras\Dbal\Utils\FileImporter;


if (@!include __DIR__ . '/../../vendor/autoload.php') {
	echo "Install Nette Tester using `composer update`\n";
	exit(1);
}

/** @var Container $container */
/** @var Connection $connection */

echo "[setup] Purging temp.\n";
@mkdir(__DIR__ . '/../tmp');
Tester\Helpers::purge(__DIR__ . '/../tmp');

$config = Neon::decode(file_get_contents(__DIR__ . '/../config.neon', TRUE));
$connection = new Connection($config['dbal']);

/** @var callable $resetFunction */
$resetFunction = require __DIR__ . "/../db/pgsql-reset.php";
$resetFunction($connection, $config['dbal']['database']);

FileImporter::executeFile($connection, __DIR__ . "/../db/pgsql-init.sql");
