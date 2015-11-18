<?php

use Nette\DI\Container;
use Nette\Neon\Neon;
use Nextras\Dbal\Connection;


if (@!include __DIR__ . '/../../vendor/autoload.php') {
	echo "Install Nette Tester using `composer update`\n";
	exit(1);
}

/** @var Container $container */
/** @var Connection $connection */


$setupMode = TRUE;

echo "[setup] Purging temp.\n";
@mkdir(__DIR__ . '/../tmp');
Tester\Helpers::purge(__DIR__ . '/../tmp');

$config = Neon::decode(file_get_contents(__DIR__ . '/../config.neon', TRUE));
