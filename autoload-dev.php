<?php

$autoloader = require __DIR__ . '/autoload-composer.php';

if (file_exists($file = __DIR__ . '/../composer-locaload.json')) {
	if ($json = @file_get_contents($file)) {
		if ($meta = @json_decode($json, true)) {
			if (isset($meta['psr-4']) && is_array($meta['psr-4'])) {
				foreach ($meta['psr-4'] as $namespace => $location) {
					$autoloader->setPsr4(rtrim($namespace, '\\') . '\\', rtrim($location, '\\/') . '/');
				}
			}
		}
	}
}

return $autoloader;
