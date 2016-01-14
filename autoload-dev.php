<?php

$autoloader = require __DIR__ . '/autoload-composer.php';

try {
	$localoadJson = new JsonFile(__DIR__ . '/../composer-locaload.json');
	$locaload = $localoadJson->read();
	foreach ($locaload as $namespace => $location) {
		$autoloader->setPsr4($namespace, $location);
	}
}
catch (Exception $ex) {
	// File doesn't exist, or is unreadable, or has invalid JSON
}

return $autoloader;
